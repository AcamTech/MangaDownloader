<?php
/**
 * Mangabird Module
 *
 * @author Rogelio Morey
 * @nickname SparoHawk
 * @created 2014-01-22
 * @version 0.1.0 alpha
 * @contact sparohawk@gmail.com
 */

namespace MangaDownloader\modules;

class Mangabird extends \MangaDownloader\MangaDownloader
{
    /**
     * @var string
     *
     * The name of the module.
     */
    public $moduleName = 'mangabird';
    
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
    public $pageListContainer = '<ul class="pager">';
    
    /**
     * @var string
     *
     * Regular expression used to extract the RAW HTML for the page listing.
     */
    public $pageListContainerRegex = '#<ul class="pager">(.*?)</ul>#is';
    
    /**
     * @var string
     *
     * Regular expression used to extract the pages
     */
    public $pageListRegex = '#<a[^>]*href="([^"]+)"[^>]*>#i';
    
    /**
     * @var string
     *
     * Regular expression for getting the image URL.
     */
    public $imageUrlRegex = '#<a[^>]*href="\?page=[0-9]+"[^>]*><img[^>]*src="([^"]+)"[^>]*#i';

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
        $this->moduleUrl = 'http://www.' . $this->moduleDomain;
        $this->url = $url;
        
        //Regex strign used to validate the URL. We want a particular structure. Modified whenever it is needed.
        $urlRegexTest = '#http://(?:www\.)?'. str_replace('.', '\.', $this->moduleDomain) .'/([a-z0-9-]+?)-([0-9]+)(?:[^\?]*)(?:\?page=[0-9]+)?#i';
        // Test that the url complies with the curent format.
        $this->testUrl($urlRegexTest);
        // Test that the webpage works and/or exists.
        $this->checkUrlHttpStatus();
        // So far so good. Start the download process.
        $this->getContent($url);
        // Get the chapter number
        $this->getChapterNumber();
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
        return $page;
    }
    
    /**
     * getChapterNumber
     *
     * Mangabird adds recurrence of chapters with corrections or modifications with decimals.
     * Need to get the chapter number from the dropdown.
     */
    public function getChapterNumber()
    {
        preg_match('#<h1[^>]*>.*?([0-9]+(?:\.[0-9])?)[^0-9]*</h1>#', $this->content, $chapterNumber);
        
        $this->chapterNumber = $chapterNumber[1];
    }
}