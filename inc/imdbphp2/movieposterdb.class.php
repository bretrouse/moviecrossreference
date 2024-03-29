<?php
 #############################################################################
 # IMDBPHP                                               (c) Itzchak Rehberg #
 # written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
 # http://www.izzysoft.de/                                                   #
 # ------------------------------------------------------------------------- #
 # Get images from movieposterdb.com                                         #
 # ------------------------------------------------------------------------- #
 # This program is free software; you can redistribute and/or modify it      #
 # under the terms of the GNU General Public License (see doc/LICENSE)       #
 #############################################################################

 /* $Id: movieposterdb.class.php 387 2010-05-23 12:31:44Z izzy $ */

 require_once (dirname(__FILE__)."/mdb_base.class.php");

#================================================[ The MoviePosterDB class ]===
/** Accessing posters, covers, etc. from movieposterdb.com
 * @package MDBApi
 * @class movieposterdb
 * @extends mdb_base
 * @author Izzy (izzysoft AT qumran DOT org)
 * @copyright (c) 2009 by Itzchak Rehberg and IzzySoft
 * @version $Revision: 387 $ $Date: 2010-05-23 14:31:44 +0200 (So, 23. Mai 2010) $
 */
class movieposterdb extends mdb_base {

#------------------------------------------------------------[ Constructor ]---
  /** Initialize class
   * @constructor movieposterdb
   * @param string id IMDBID to use for data retrieval
   * @param optional integer limit maximum amount of images to retrieve
   *        (default: 20). Can be changed via set_limit()
   * @param optional boolean recurse whether to recurse if multiple versions
   *        are available (default: TRUE). Can be changed via set_recurse()
   */
  function __construct($id,$limit=20,$recurse=TRUE) {
    parent::__construct($id);
    $this->setid($id);
    $this->reset_vars();
    $this->set_limit($limit);
    $this->set_recurse($recurse);
    $this->urlparams = array(
      'poster'   => 'cid=1',
      'cover'    => 'cid=2',
      'textless' => 'cid=3',
      'logo'     => 'cid=4',
      'other'    => 'cid=5',
      'custom'   => 'cid=6',
      'unset'    => 'cid=9'
    );
    $this->reset_lang();
    if ( in_array('HTTP_USER_AGENT',array_keys($_SERVER)) ) $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
    else $this->user_agent = 'Mozilla/5.0 (X11; U; Linux i686; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3';
    $this->image_exts = array('jpg','png','gif','bmp');
  }

#----------------------------------------------------------------[ Helpers ]---
  /** Find the base pages URL
   * @method protected get_baseurl
   */
  protected function get_baseurls() {
    $this->getWebPage('base','http://www.movieposterdb.com/browse/search?type=movies&query='.$this->imdbid());
    $doc = new DOMDocument();
    @$doc->loadHTML($this->page['base']);
    $xp = new DOMXPath($doc);
    $nodes = $xp->query("//a[contains(@href,".$this->imdbid().")]");
    foreach ($nodes as $node) $this->baseurls[] = $node->getAttribute('href');
  }

  /** Parse page for images
   * @method protected parse_list
   * @param optional string type (what image URLs to retrieve: 'poster' (default),
   *        'cover', 'textless', 'logo', 'other', 'unset')
   * @return array array of arrays[lang,url]
   */
  protected function parse_list($type='poster',$page_url='') {
    if ( empty($this->baseurls) ) $this->get_baseurls();
    if ( empty($this->baseurls) ) return array();
    $http = array(
      'method' => 'GET',
      'header'  => "Accept-Language: en-en;q=0.8,en-us;q=0.5,en;q=0.3\r\nAccept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\nReferer: ".$this->baseurls[0]."\r\n",
      'user_agent' => $this->user_agent
    );
    if ( empty($page_url) ) $page_url = $this->baseurls[0].'?'.$this->urlparams[$type];
    $page = file_get_contents($page_url,false,stream_context_create(array('http'=>$http)));
    $doc = new DOMDocument();
    @$doc->loadHTML($page);
    $xp = new DOMXPath($doc);
    $nodes = $xp->query("//td[starts-with(@id,'poster')]/div/a");
    $urls = array();
    foreach ($nodes as $node) {
      $url = $node->getAttribute('href');
      if ( preg_match('|'.$this->urlparams[$type].'$|',$url) ) { // multiple variants
        if ($this->recurse) $urls = array_merge($urls,$this->parse_list($type,$url));
      } else {
        $lnode = $node->parentNode->nextSibling;
        while (strtolower($lnode->tagName)!='p') $lnode = $lnode->nextSibling;
        $lnode = $lnode->firstChild;
        while (strtolower($lnode->tagName)!='img') $lnode = $lnode->nextSibling;
        $lang = strtolower($lnode->getAttribute('alt'));
        if (!empty($this->langs)) {
          if ( !in_array($lang,$this->langs) ) continue;
        }
        $img['lang'] = $lang;
        $img['url']  = $this->get_img($url);
        $urls[] = $img;
      }
      if (count($urls)>=$this->limit) return $urls;
    }
    return $urls;
  }

  /** Get image source URL
   *  (Helper to parse_list)
   * @method protected get_img
   * @param string url of the page where the image is embedded into
   * @return string imgurl
   */
  protected function get_img($url) {
    $http = array(
      'method' => 'GET',
      'header'  => "Accept-Language: en-en;q=0.8,en-us;q=0.5,en;q=0.3\r\nAccept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\nReferer: ".$this->baseurls[0]."\r\n",
      'user_agent' => $this->user_agent
    );
    $page = file_get_contents($url,false,stream_context_create(array('http'=>$http)));
    $doc = new DOMDocument();
    @$doc->loadHTML($page);
    $xp = new DOMXPath($doc);
    $nodes = $xp->query("//div[@class='mainwindow']/div/img");
    foreach ($nodes as $node) {
      return $node->getAttribute('src');
    }
  }

#---------------------------------------------------------[ public methods ]---
  /** Get the posters
   * @method public posters
   * @return array array of arrays[lang,url]
   */
  public function posters() {
    return $this->parse_list('poster');
  }

  /** Get the cover images
   * @method public covers
   * @return array array of arrays[lang,url]
   */
  public function covers() {
    return $this->parse_list('cover');
  }

  /** Get the logos
   * @method public logos
   * @return array array of arrays[lang,url]
   */
  public function logos() {
    return $this->parse_list('logo');
  }

  /** Get the textless images
   * @method public textless
   * @return array array of arrays[lang,url]
   */
  public function textless() {
    return $this->parse_list('textless');
  }

  /** Get the images having no category set
   * @method public unsets
   * @return array array of arrays[lang,url]
   */
  public function unsets() {
    return $this->parse_list('unset');
  }

  /** Get the "other" images
   * @method public others
   * @return array array of arrays[lang,url]
   */
  public function others() {
    return $this->parse_list('other');
  }

  /** Reset everything to start a new search
   * @method public reset_vars
   */
  public function reset_vars() {
    $this->page = array();
    $this->baseurls = array(); // URLs of the base page(s) for this IMDBID. Should usually be only one.
  }

  /** Set a limit (maximum images to retrieve)
   * @method public set_limit
   * @param optional integer limit (default: 20)
   */
  public function set_limit($limit=20) {
    $this->limit = $limit;
  }

  /** Shall we recurse if multiple versions are offered?
   * @method public set_recurse
   * @param optional boolean recurse (default: TRUE)
   */
  public function set_recurse($recurse=TRUE) {
    $this->recurse = $recurse;
  }

  /** Restrict images to given languages. By default, no restriction is active;
   *  you may call this method multiple times to add multiple languages (e.g.
   *  your native plus EN plus US), and use the reset_lang method to clean.
   * @method public add_lang
   * @param string lang two-letter ISO language code
   */
  public function add_lang($lang) {
    $this->langs[] = strtolower($lang);
  }

  /** Reset the language filter built by the add_lang method to its default (i.e. empty)
   * @method public reset_lang
   */
  public function reset_lang() {
    $this->langs = array();
  }

  /** Save an image to the image dir
   *  The image will be retrieved from the passed URL and stored in the configured
   *  mdb::photodir, using the filename part of the URL specified.
   * @method public save_image
   * @param string url full URL to the image
   * @return boolean success
   */
  public function save_image($url) {
    if ( empty($this->photodir) ) return FALSE;
    $furl  = explode('/',$url);
    $fname = $furl[count($furl)-1];
    $ext   = explode('.',$fname);
    $fext  = $ext[count($ext)-1];
    if ( !in_array(strtolower($fext),$this->image_exts) ) return FALSE;
    echo "File: $fname<br>Ext: $fext<br>";
    if ( file_put_contents($this->photodir.$fname, file_get_contents($url)) ) return TRUE;
    return FALSE;
  }

}