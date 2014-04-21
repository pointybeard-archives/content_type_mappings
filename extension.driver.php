<?php

	Final Class extension_Content_Type_Mappings extends Extension{
		
		public function about(){
			return array('name' => 'Content Type Mappings',
						 'version' => '1.3',
						 'release-date' => '2009-04-30',
						 'author' => array('name' => 'Symphony Team',
										   'website' => 'http://www.symphony21.com',
										   'email' => 'team@symphony21.com')
				 		);
		}

		public function install(){
			
			$initial_mappings = array(
				'xml' => 'text/xml; charset=utf-8',
				'text' => 'text/plain; charset=utf-8'
			);
			
			foreach($initial_mappings as $type => $content_type){
				Symphony::Configuration()->set($type, $content_type, 'content-type-mappings');
			}
			
			Administration::saveConfig();	
		}	

		public function uninstall(){
			Symphony::Configuration()->remove('content-type-mappings');			
			Administration::saveConfig();
		}

		public function resolveType($type){
			return Symphony::Configuration()->get(strtolower($type), 'content-type-mappings');
		}
		
		public function getSubscribedDelegates(){
			return array(
						array(
							'page' => '/frontend/',
							'delegate' => 'FrontendPreRenderHeaders',
							'callback' => 'setContentType'							
						),						
			); 
		}
		
		public function setContentType(array $context=NULL){
			$page_data = Frontend::Page()->pageData();
			
			if(!isset($page_data['type']) || !is_array($page_data['type']) || empty($page_data['type'])) return;
			
			foreach($page_data['type'] as $type){
				$content_type = $this->resolveType($type);
				
				if(!is_null($content_type)){	
					Frontend::Page()->addHeaderToPage('Content-Type', $content_type);
				}
				
				if($type{0} == '.'){  
					$FileName = $page_data['handle'];
					Frontend::Page()->addHeaderToPage('Content-Disposition', "attachment; filename={$FileName}{$type}");
				}
			}
		}

	}

