<?php
/**
 * Manga Downloader
 *
 * @author Rogelio Morey
 * @nickname SparoHawk
 * @created 2014-01-22
 * @version 0.1.1 beta
 * @contact sparohawk@gmail.com
 *
 * The purpose of this script is to download full chapters from the following manga hosting websites:
 *     - Mangareader.com
 *     - Mangastream.com
 *     - Mangafox.me
 *     - Mangabird.com
 *     - Onemanga.me
 *
 * To the corresponding site admins:
 *     I'd allow you to message me to remove your site from the list, but I won't.
 *
 *     Reason being is simple: you host illegal content and therefore have no right to claim the
 *     removal of your site from this script.
 */

namespace MangaDownloader;

class MangaDownloader
{
    /**
     * @var array
     *
     * List of all supported sites
     */
    public $sites = [
        'mangareader',
        'mangastream',
        'mangafox',
        'mangabird',
        'onemanga',
        'batoto',
        'submanga',
        'ehentai'
    ];
    
    /**
     * @var string
     *
     * The url provided by the user.
     */
    public $url = '';
    
    /**
     * @var string
     *
     * The content of the downloaded pages
     */
    public $content = '';
    
    /**
     * @var array
     *
     * List of pages
     */
    public $pageList = [];
    
    /**
     * @var int
     *
     * Total pages extracted
     */
    public $totalPages = 0;
    
    /**
     * @var string
     *
     * Name of the series
     */
    public $seriesName = '';
    
    /**
     * @var int
     *
     * Chapter number.
     */
    public $chapterNumber = 0;
    
    /**
     * @var string
     *
     * Folder name. Used to store images.
     */
    public $folder = '';

    /**
     * Function constructor includes all site modules and initializes them
     *
     * @param string $url
     *
     * @return void
     */
    public function __construct($url)
    {
        // Check for valid URL
        if(filter_var($url, FILTER_VALIDATE_URL))
        {
            // Extract the domain for
            // NOTE: For now we don't need the ending part of the domain.
            preg_match('#http://(?:[a-z0-9-]+\.)?([a-z0-9-]+)\.[a-z]{2,3}(?:\.[a-z]{2})?#', $url, $domain);
            
            // Replace dashes
            $domain[1] = str_replace('-', '', $domain[1]);
            // Check that it is a supported domain
            if(in_array($domain[1], $this->sites))
            {
                // Everything is good. Just instance the module and let it rip. XD
                $module = '\MangaDownloader\modules\\'.ucfirst($domain[1]);
                new $module($url);
            }
            else
            {
                throw new \Exception('Site is not supported. Please contact me about this so I can add it. :)');
            }
        }
        else
        {
            throw new \Exception('Invalid URL.');
        }
    }
    
    /**
     * testUrl
     *
     * Function to test that the provided URL complies with the current URL structure.
     *
     * @param string $url
     *
     * @return void
     */
    public function testUrl($urlRegexTest)
    {
        if(!preg_match($urlRegexTest, $this->url, $urlParts))
            throw new \Exception('The provided "' . ucfirst($this->moduleName) . '" url is not a valid URL.');
        
        $this->seriesName = $urlParts[1];
        $this->chapterNumber = $urlParts[2];
    }
    
    /**
     * checkUrlStatus
     *
     * Verify that the webpage exists and is error free
     *
     * @return void
     */
    public function checkUrlHttpStatus()
    {
        $httpStatus = substr(get_headers($this->url)[0], 9, 3);
        
        if($httpStatus != '200')
            throw new \Exception('The provided ' . ucfirst($this->moduleName) . ' returns a ' . $httpStatus . ' HTTP Status.');
    }
    
    /**
     * getContent
     *
     * Function to fetch content. Download the page.
     *
     * @return void
     */
    public function getContent($url)
    {
        $this->content = file_get_contents($url);
    }
    
    /**
     * checkForPageList
     *
     * Check that the HTML for the page list is present. Extra checks and stuff.
     *
     * @return void
     */
    public function checkForPageList()
    {
        if(stristr($this->content, $this->pageListContainer) === false)
            throw new \Exception('The downlaoded content for the provided URL does not contain the page listing.');
    }
    
    /**
     * getPageListContent
     *
     * Get the RAW HTML of the page listing.
     *
     * @return void
     */
    public function getPageListHtml()
    {
        preg_match($this->pageListContainerRegex, $this->content, $pageListHtml);
        
        $this->content = $pageListHtml[1];
    }
    
    /**
     * getPageList
     *
     * Get the page list.
     *
     * @return void
     */
    public function getPageList()
    {
        preg_match_all($this->pageListRegex, $this->content, $pageList);
        
        $this->pageList = $pageList[1];
        $this->totalPages = count($this->pageList);
    }
    
    /**
     * createFolder
     *
     * Create a new folder where to save the images.
     *
     * @return void
     */
    public function createFolder()
    {
        $this->folder = $this->seriesName.'-'.$this->chapterNumber;
        
        if(!mkdir($this->folder))
            throw new \Exception('Error while creating folder. Please check permissions.');
    }
    
    /**
     * download
     *
     * Function for dowloading the pages.
     *
     * @return void
     */
    public function download()
    {
        foreach($this->pageList as $k => $page)
        {
            // Get the contents
            $this->getContent($this->getUrl($page));
            // Find the image URL
            $imageUrl = $this->getImageUrl();
            // Now get the image and store it in the folder
            file_put_contents($this->folder . '/' . ($k+1) . '.jpg', file_get_contents($imageUrl));
            // Sleep for 2 seconds to avoid flooding and a possible ban
            // Therefore let's tell where we are:
            echo 'Downloaded page ',($k+1),' of ',$this->totalPages,': ',$page,"\n";
            sleep(2);
        }
    }
    
    /**
     * zip
     *
     * Zip everything up and we are done.
     *
     * @return void
     */
    public function zip()
    {
        exec('zip -9r ' . $this->folder . '.zip ' . $this->folder . '/');
        echo "ZIP file created. All done here. :)\n";
    }
    
    /**
     * getImageUrl
     *
     * Get the URL of the image in the content.
     *
     * @return string
     */
    public function getImageUrl()
    {
        preg_match($this->imageUrlRegex, $this->content, $urls);
        
        return $urls[1];
    }
    
    /**
     * addTrailingSlash
     *
     * Some sites require the trailing slash in order to allow a proper page download (looking at you Batoto ¬¬).
     *
     * @param string $url
     */
    public function addTrailingSlash($url)
    {
        if(substr($url, -1) != '/')
            $url .= '/';
        
        return $url;
    }
}