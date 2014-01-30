<?php
/**
 * Submanga Module
 *
 * @author Rogelio Morey
 * @nickname SparoHawk
 * @created 2014-01-22
 * @version 0.1.1 beta
 * @contact sparohawk@gmail.com
 */

namespace MangaDownloader\modules;

class Submanga extends \MangaDownloader\MangaDownloader
{
    /**
     * @var string
     *
     * The name of the module.
     */
    public $moduleName = 'submanga';
    
    /**
     * @var string
     *
     * The name of the domain
     */
    public $moduleDomain;

    /**
     * @var boolean
     *
     * Tells the application that a page list is available.
     */
    public $moduleUrl;
    
    /**
     * @var string
     *
     * Container containing the list of pages we need.
     */
    public $pageListContainer = '<select onchange';
    
    /**
     * @var string
     *
     * Regular expression used to extract the RAW HTML for the page listing.
     */
    public $pageListContainerRegex = '#<select[^>]*onchange[^>]*>(.*?)</select>#is';
    
    /**
     * @var string
     *
     * Regular expression used to extract the pages
     */
    public $pageListRegex = '#<option[^>]*value="([^"]+)"[^>]*>#i';
    
    /**
     * @var string
     *
     * Regular expression for getting the image URL.
     */
    public $imageUrlRegex = '#<img[^>]*src="(http[^"]*)"[^>]*>#i';

    /**
     * Initialize the process to download a manga from Mangareader
     *
     * @param string $url
     *
     * @return void
     */
    public function __construct($url)
    {
        // Set module variables.
        $this->moduleDomain = $this->moduleName . '.com';
        $this->moduleUrl = 'http://' . $this->moduleDomain;
        $this->url = $url;
        
        //Regex strign used to validate the URL. We want a particular structure. Modified whenever it is needed.
        $urlRegexTest = '#http://'. str_replace('.', '\.', $this->moduleDomain) .'/(c)/([0-9]+)(?:/[0-9]+)?#i';
        // Test that the url complies with the curent format.
        $this->testUrl($urlRegexTest);
        // Test that the webpage works and/or exists.
        $this->checkUrlHttpStatus();
        // So far so good. Start the download process.
        $this->getContent($url);
        // Fix Url
        $this->fixUrl();
        // Submanga has wierd stuff.
        $this->getFileInfo();
        // Further checking to ensure correct HTML.
        $this->checkForPageList();
        // Get that page listing HTML.
        $this->getPageListHtml();
        // Get the page list
        $this->getPageList();
        // Create the folder
        $this->createFolder();
        // Finally! Time to download all those pages. Let's go!
        $this->download();
        // Zip it up
        $this->zip();
    }
    
    /**
     * getUrl
     *
     * Return the corresponding URL of the page.
     *
     * @return string
     */
    public function getUrl($page)
    {
        return $this->url . '/' . $page;
    }
    
    /**
     * fixUrl
     *
     * Have to make url from provided URL, no other way.
     *
     * @return void
     */
    public function fixUrl()
    {
        preg_match('#^(' . str_replace('.', '\.', $this->moduleUrl) . '/c/[0-9]+)#', $this->url, $url);
        
        $this->url = $url[1];
    }
    
    /**
     * getFileInfo
     *
     * Functio to get information from submanga, since they don't provide any through the URL.
     *
     * @return void
     */
    public function getFileInfo()
    {
        preg_match('#<a[^>]*href="\./([^/]*)/([a-z0-9]+)/[0-9]+">\2</a>#i', $this->content, $fileParts);
        
        $this->seriesName = $fileParts[1];
        $this->chapterNumber = $fileParts[2];
    }
}