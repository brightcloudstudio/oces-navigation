<?php

/**
 * Bright Cloud Studio's OCES Navigation
 * Copyright (C) 2025 Bright Cloud Studio
 * @package    bright-cloud-studio/oces-navigation
 * @link       https://www.brightcloudstudio.com/
**/

  
namespace Bcs\Module;
 
use Bcs\Model\NavigationOption;

use Contao\BackendTemplate;
use Contao\FrontendTemplate;
use Contao\PageModel;
use Contao\System;
 
class NavigationOptionModule extends \Contao\Module
{
 
    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'mod_oces_navigation';
 
	protected $arrStates = array();
 
	/**
	 * Initialize the object
	 *
	 * @param \ModuleModel $objModule
	 * @param string       $strColumn
	 */
	public function __construct($objModule, $strColumn='main')
	{
		parent::__construct($objModule, $strColumn);
		//$this->arrStates = Locations::getStates();
	}
	
    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();
		if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
		{
            $objTemplate = new BackendTemplate('be_wildcard');
 
            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['navigation_option'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;
 
            return $objTemplate->parse();
        }
 
        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        // Add our custom js file
        $GLOBALS['TL_BODY'][] = '<script src="/bundles/bcsocesnavigation/js/mod_oces_navigation.js"></script>';
        //$GLOBALS['TL_BODY'][] = '<script src="system/modules/oces_navigation/assets/js/mod_oces_navigation.js"></script>';


        
        // Sort our Listings based on the 'last_name' field
        $options = [
            'order' => 'id ASC'
        ];
        // Get all of the navigation options
        $objNavigationOptions = NavigationOption::findBy('published', '1', $options);

        // Store our options here
        $arrSelectOptions = array();
        
        // Make our first blank option
        $arrBlankOptiom = array();
        $arrBlankOptiom['label'] = 'I am...';
        $arrBlankOptiom['id'] = 0;
        $strListTemplate = ($this->entry_customItemTpl != '' ? $this->entry_customItemTpl : 'item_select_option');
        $objListTemplate = new FrontendTemplate($strListTemplate);
        $objListTemplate->setData($arrBlankOptiom);
        $arrSelectOptions[0] = $objListTemplate->parse();
        


        // Loop through out options
        foreach ($objNavigationOptions as $option)
		{
            // Temporary array to store our details
		    $arrOption = array();

            // Assign our details
            $arrOption['id']                   = $option->id;
            $arrOption['label']                = $option->label;
            $arrOption['target_anchor']        = $option->target_anchor;

            // Get the Contao page by passing in our pageTree value
            $objPage = PageModel::findByPk($option->target_page);
            if ($objPage) {
                // Save our frontend url
                $arrOption['target_page'] = $objPage->getFrontendUrl();
            }
            
            // Pass our stored values into our item template
            $strListTemplate = ($this->entry_customItemTpl != '' ? $this->entry_customItemTpl : 'item_select_option');
            $objListTemplate = new FrontendTemplate($strListTemplate);
            $objListTemplate->setData($arrOption);
            $arrSelectOptions[$option->id] = $objListTemplate->parse();

		}
        // Save our templated option to this module's template
        $this->Template->select_options = $arrSelectOptions;

	}

} 
