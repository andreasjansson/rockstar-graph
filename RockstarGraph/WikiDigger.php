<?php

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
  public function findBandMembers($band)
  {
    $xml = $this->getXML($band);
    $infoBox = $xml->xpath('//table[@class="infobox vcard"]');

    if(!isset($infoBox[0]))
      throw new RockstarGraph_WikiException('No info box on page: ' . $band);
    $infoBox = $infoBox[0];

    $membersBox = $infoBox->
      xpath('.//th[text() = "Members"]/parent::*/following-sibling::*[1]');
    if(isset($membersBox[0])) {
      $currentMembers = $this->membersFromBox($membersBox[0]);
      $members = $currentMembers;
    }
    else
      $members = array();

    $pastMembersBox = $infoBox->
      xpath('.//th[text() = "Past members"]/parent::*/following-sibling::*[1]');
    if(isset($pastMembersBox[0])) {
      $pastMembers = $this->membersFromBox($pastMembersBox[0]);
      $members = array_unique(array_merge($members, $pastMembers));
    }
      
    return $members;
  }

  /**
   * @param string $rockstar url
   * @return string[]
   */
  public function findAssociatedActs($rockstar)
  {
    $associatedActs = array();

    $xml = $this->getXML($rockstar);
    $infoBox = $xml->xpath('//table[@class="infobox vcard"]');

    if(!isset($infoBox[0]))
      throw new RockstarGraph_WikiException(
        'No info box on page: ' . $rockstar);
    $infoBox = $infoBox[0];

    $associatedActsElements = $infoBox->
      xpath('.//th[text() = "Associated acts"]/following-sibling::*[1]/' .
            'a[starts-with(@href, "/wiki/")]');
    foreach($associatedActsElements as $element) {
      $name = $this->nameFromElementHref($element);
      $associatedActs[] = $name;
    }

    return $associatedActs;
  }

  /**
   * @param SimpleXMLElement $box
   * @return string[]
   */
  private function membersFromBox(SimpleXMLElement $box)
  {
    $members = array();

    $membersElements =  $box->xpath('.//a[starts-with(@href, "/wiki/")]');

    foreach($membersElements as $element) {
      $name = $this->nameFromElementHref($element);

      if(!empty($name))
        $members[] = $name;
    }

    return $members;
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