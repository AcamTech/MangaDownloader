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

class Ehentai extends \MangaDownloader\MangaDownloader
{
    /**
     * @var string
     *
     * The name of the module.
     */
    public $moduleName = 'g.e-hentai';
    
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
    public $pageListContainer = '<table class="ptt"';
    
    /**
     * @var string
     *
     * Regular expression used to extract the RAW HTML for the page listing.
     */
    public $pageListContainerRegex = '';
    
    /**
     * @var string
     *
     * Regular expression used to extract the pages
     */
    public $pageListRegex = '';
    
    /**
     * @var string
     *
     * Regular expression for getting the image URL.
     */
    public $imageUrlRegex = '#<img[^>]*id="img"[^>]*src="([^"]*)"[^>]*>#i';

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
        $this->moduleDomain = $this->moduleName . '.org';
        $this->moduleUrl = 'http://' . $this->moduleDomain;
        $this->url = $url;
        
        //Regex strign used to validate the URL. We want a particular structure. Modified whenever it is needed.
        $urlRegexTest = '#http://'. str_replace('.', '\.', $this->moduleDomain) .'/(g|s)/([a-z0-9]+)/[a-z0-9]+(?:-[0-9]+|/?(?:\?p=[0-9]+)?)#i';
        // Test that the url complies with the curent format.
        $this->testUrl($urlRegexTest);
        // Test that the webpage works and/or exists.
        $this->checkUrlHttpStatus();
        // So far so good. Start the download process.
        $this->getContent($this->url);
        
        /**
         * E-Hentai specifics.
         * Can either recieve a single image URL or a gallery URL. Need to know what to do.
         */
        $this->getMainUrl();
        // E_henati has wierd stuff.
        $this->getFileInfo();
        
        // Further checking to ensure correct HTML.
        $this->checkForPageList();
        
        /**
         * E-Hentai specifics.
         * Fun part. Multiple pages with multiple links.
         */
        // Get that page listing HTML.
        $this->getGalleryPages();
        
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
        return $page;
    }
    
    /**
     * getMainUrl
     *
     * Function to get the main URL in case we are in a particular page.
     *
     * @return void
     */
    public function getMainUrl()
    {
        // If seriesName contains "s", that means we are in a single image.
        if($this->seriesName == 's')
        {
            preg_match('#<a[^>]*href="([^"]+)"[^>]*><img[^>]*src="[^"]*b\.png"#i', $this->content, $url);
            
            if($qPos = strrpos($url[1], '?') !== false)
                $this->url = substr($url[1], 0, strrpos($url[1], '?'));
            else
                $this->url = $url[1];
            
            // Have to refresh the content
            $this->getContent($this->url);
        }
    }
    
    /**
     * getPageListHtml
     *
     * Get the list of gallery pages with images.
     *
     * @return void
     */
    public function getGalleryPages()
    {
        // First get the table with the pages
        preg_match('#<table[^>]*class="ptt"[^>]*>(.*?)</table>#i', $this->content, $pageContainerHtml);
        // Next get the <td>s.
        preg_match_all('#<td[^>]*><a[^>]*href="[^"]*"[^>]*>([0-9]+)</a></td>#', $pageContainerHtml[1], $pageListHtml);
        
        $lastPage = end($pageListHtml[1]);
        
        for($i = 0; $i < $lastPage; $i++)
        {
            $page = $this->url . '?p=' . $i;
            
            $this->getPageLinks($page);
            sleep(2);
        }
    }
    
    /**
     * getPageList
     *
     * Overwrite parent function for different functionality.
     *
     * @return void
     */
    public function getPageLinks($page)
    {
        $content = file_get_contents($page);
        
        preg_match_all('#<div[^>]*class="gdtm"[^>]*>.*?<a[^>]*href="([^"]+)"[^>]*>#i', $content, $links);
        
        $this->pageList = array_merge($this->pageList, $links[1]);
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
        preg_match('#<h1 id="gn">(.*?)</h1>#i', $this->content, $fileParts);
        
        $this->seriesName = trim(preg_replace('#^-|-$#', '', preg_replace('#-{2,}#', '-', preg_replace('#\W#', '-', $fileParts[1]))));
        $this->chapterNumber = '';
    }
}