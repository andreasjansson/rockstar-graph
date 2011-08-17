<?php

/**
 * Currently mostly scraping wikipedia.  TODO: Use Mediawiki API for
 * everything (exportnowrap).  (if the documentation gets updated to
 * actually reflect the current interface...)
 */
class RockstarGraph_WikiDigger
{
  public function __construct()
  {
    libxml_use_internal_errors(TRUE);
  }

  /**
   * @param string $band url
   * @return string[]
   */
  public function findAssociatedActs($band)
  {
    $associatedActs = array();

    $xml = $this->getXML($band);
    $infoBox = $xml->xpath('//table[@class="infobox vcard"]');

    if(!isset($infoBox[0]))
      throw new RockstarGraph_WikiException(
        'No info box on page: ' . $band);
    $infoBox = $infoBox[0];

    $associatedActsElements = $infoBox->
      xpath('.//th[text() = "Associated acts"]/following-sibling::*[1]/' .
            'a[starts-with(@href, "/wiki/")]');
    foreach($associatedActsElements as $element) {
      $name = $this->nameFromElementHref($element);
      $name = $this->getCanonicalName($name);
      $associatedActs[] = $name;
    }

    return $associatedActs;
  }

  /**
   * @param string $name
   * @return string
   */
  public function getCanonicalName($name)
  {
    $curl = curl_init(
      'http://en.wikipedia.org/w/api.php?action=query&rvprop=content&' .
      'format=xml&redirects&titles=' . $name);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERAGENT, 'RockstarGraph; ' .
                'andreas@jansson.me.uk');
    $data = curl_exec($curl);
    curl_close($curl);

    if(!$data)
      throw new RockstarGraph_WikiException('Failed to get canonical name');

    $xml = new SimpleXMLElement($data);
    $redirect = $xml->xpath('//redirects/r[1]');
    if(empty($redirect))
      return $name;

    $redirect = $redirect[0];
    $to = $redirect['to'];
    $to = str_replace(' ', '_', $to); // hacky

    return $to;
  }

  /**
   * @param SimpleXMLElement $element [starts0with(@href, "/wiki")]
   * @return string
   */
  private function nameFromElementHref(SimpleXMLElement $element)
  {
    $href = $element['href'];
    $name = substr($href, 6);
    return $name;
  }

  /**
   * @param string url
   * @return SimpleXMLElement
   */
  private function getXML($url)
  {
    $url = 'http://en.wikipedia.org/wiki/' . urlencode($url);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
    curl_setopt($curl, CURLOPT_USERAGENT,
                "If you update your API documentation I'll use the API. " .
                "exportnowrap doesn't work as expected (http://www." .
                "mediawiki.org/wiki/API:Query#Exporting_pages). " .
                "andreas@jansson.me.uk");
    $data = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if($httpCode != 200 || empty($data))
      throw new RockstarGraph_WikiException(
        'Failed to retrieve article: ' . $url);

    // http://www.php.net/manual/en/simplexmlelement.xpath.php#96153
    $data = str_replace('xmlns=', 'ns=', $data);
    $xml = new SimpleXMLElement($data);

    return $xml;
  }
}

class RockstarGraph_WikiException extends Exception 
{
  public function __construct($message)
  {
    parent::__construct($message);
    file_put_contents('wiki_exceptions.log', $message . "\n", FILE_APPEND);
  }
}