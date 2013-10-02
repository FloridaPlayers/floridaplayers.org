<?php

class Page{
	var $request = null;
	var $sql = null;

	function Page($request){
		$this->request = $request;
	}
	
	//Return string containing the page's title. 
	function getTitle(){
		echo "Resources";
	}

	//Page specific content for the <head> section.
	function customHead(){?>
		<style type="text/css">
			ul.resourceList{
				list-style: none outside none;
				padding: 5px 10px 10px;
				margin: 10px 0;
				border: 1px solid #DDDDDD;
				color: #9A9A9A;
			}
			.resourceList li{
				border-top: 1px solid #DDDDDD;
				display: block;
				font-family: AurulentSansRegular;
				font-size: 15px;
				font-weight: normal;
				margin: 5px 0 0;
				padding: 12px 0 2px 35px;
			}
			.resourceList li:first-of-type{
				border-top: 0px solid #fff;
				margin: 0;
			}
			.resourceList li.directory{
				padding-left: 35px;
				background: url('/res/images/layout/res-folder.png') no-repeat bottom left transparent;
			}
			.resourceList li.indir{
				margin-left: 35px;
			}
			.resourceList li.type-none{
				background: url('/res/images/layout/res-file.png') no-repeat bottom left transparent;
			}
			.resourceList li.type-pdf{
				background: url('/res/images/layout/res-pdf.png') no-repeat bottom left transparent;
			}
			.resourceList li.type-doc{
				background: url('/res/images/layout/res-doc.png') no-repeat bottom left transparent;
			}
			.resourceList li.type-zip{
				background: url('/res/images/layout/res-zip.png') no-repeat bottom left transparent;
			}
			.resourceList li.type-link{
				background: url('/res/images/layout/res-link.png') no-repeat bottom left transparent;
			}
			.resourceList li.type-globe{
				background: url('/res/images/layout/res-globe.png') no-repeat bottom left transparent;
			}
			.resourceList li a{
				display: block;
				color: #9A9A9A !important;
			}
		</style>
			<!-- CUSTOM CSS/LINK HERE -->
	<?php
	}


	/**
	 * This function gets called before anything else. 
	 * If it returns false, the page will stop loading.
	 **/
	function preloadPage(){
		$this->sql = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
		$this->sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return true; 
	}

	/**
	 * If desired, you may override the template being used
	 * return a string containing the directory path, or false for no override.
	 **/
	function getOverrideTemplate(){
		return false;
		//return SERVER_RES_DIR.'template.html';
	}


	//Return nothing; print out the page. 
	function getContent(){
		?>
		<h1>Resources</h1>
		<article>
			<section>
				<h2>Links</h2>
				<ul class='resourceList'>
				<?php
				$dir = '';
				$query = 'SELECT * FROM resources WHERE type=\'link\' ORDER BY dir,modified';
				foreach($this->sql->query($query) as $row){
					$type = $row['icon'];
					if($type == 'none'){ $type = 'link'; }
					
					if($row['dir'] != $dir){
						$dir = $row['dir'];
						printf('<li class="directory">%s</li>',$dir);
					}
					if($dir == ''){
						printf('<li class="%s"><a href="%s">%s</a></li>','type-'.$type,$row['path'],$row['name']);
					}
					else{
						printf('<li class="indir %s"><a href="%s">%s</a></li>','type-'.$type,$row['path'],$row['name']);
					}
				}
				?>
				</ul>
				<div class="clear"></div>
			</section>
			<section>
				<h2>Files</h2>
				<ul class='resourceList'>
				<?php
				$dir = '';
				$query = 'SELECT * FROM resources WHERE type=\'file\' ORDER BY dir,modified';
				foreach($this->sql->query($query) as $row){
					if($row['dir'] != $dir){
						$dir = $row['dir'];
						printf('<li class="directory">%s</li>',$dir);
					}
					if($dir == ''){
						printf('<li class="%s"><a href="%s">%s</a></li>','type-'.$row['icon'],$row['path'],$row['name']);
					}
					else{
						printf('<li class="indir %s"><a href="%s">%s</a></li>','type-'.$row['icon'],$row['path'],$row['name']);
					}
				}
				?>
				</ul>
				<div class="clear"></div>
			</section>
		</article>
		<?php
	}
}
?>