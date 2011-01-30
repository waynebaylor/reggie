<?php

class template_admin_FileUpload extends template_AdminPage
{
	private $event;
	
	function __construct($event) {
		parent::__construct('Upload File');
		
		$this->event = $event;
	}
	
	protected function getBreadcrumbs() {
		$breadcrumbs = new fragment_Breadcrumb(array(
			'location' => 'FileUpload',
			'eventId' => $this->event['id'],
			'eventCode' => $this->event['code']
		));
		
		return $breadcrumbs;
	}
	
	protected function getContent() {
		return <<<_
			<div id="content">
				<div class="file-upload">
					<h3>Upload file for {$this->event['code']}</h3>
					
					<form method="post" enctype="multipart/form-data" action="{$this->contextUrl('/admin/fileUpload/FileUpload')}">
						{$this->HTML->hidden(array(
							'name' => 'id',
							'value' => $this->event['id']
						))}
						
						<table>
							<tr>
								<td class="label">File</td>
								<td>
									<input type="file" name="file">								
								</td>
							</tr>
							<tr>
								<td></td>
								<td>
									<div class="sub-divider"></div>
									
									{$this->HTML->hidden(array(
										'name' => 'a',
										'value' => 'saveFile'
									))}
									
									<input type="submit" class="button" value="Upload">
								</td>
							</tr>
						</table>
					</form>
				</div>
				
				<div class="divider"></div>
				
				<h3>Files</h3>
				
				<div class="file-list">
					<table class="admin">
						<tr>
							<th>Name</th>
							<th>Link</th>
							<th>Options</th>
						</tr>
						
						{$this->getFileRows()}
					</table>
				</div>
			</div>
_;
	}
	
	private function getFileRows() {
		$html = '';
		$evenRow = true;
		
		$files = FileUtil::getEventFiles($this->event);
		foreach($files as $file) {
			$evenRow = !$evenRow;
			$rowClass = $evenRow? 'even' : 'odd';
			
			$protocol = in_array(Config::$MODE_SSL, Config::$SETTINGS['MODE'])? 'https://' : 'http://';
			$url = $this->contextUrl('/files/'.$this->event['code'].'/'.$file);
			$link = $protocol.$_SERVER['SERVER_NAME'].$url;
			
			$html .= <<<_
				<tr class="{$rowClass}">
					<td>
						{$file}
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => $link,
							'href' => $url,
							'target' => '_blank',
						))}
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Delete',
							'href' => '/admin/fileUpload/FileUpload',
							'parameters' => array(
								'id' => $this->event['id'],
								'fileName' => $file,
								'a' => 'deleteFile'
							),
							'class' => 'remove'
						))}
					</td>
				</tr>
_;
		}
		
		return $html;
	}
}

?>