<?php
// Скрипт установки

class mod_mycityselectorInstallerScript {
	
	public function __construct( $adapter ){
	}

	public function preflight( $route,  $adapter ){
		return true;
	}

	public function postflight( $route,  $adapter ){
		return true;
	}

	public function install( $adapter ){
		return true;
	}

	public function update( $adapter ){
		return true;
	}

	public function uninstall( $adapter ){
		// удаляем директорию templates
		// http://stackoverflow.com/questions/3349753/delete-directory-with-files-in-it
		$dir = realpath(dirname(__FILE__)) . '/templates/';
		$it = new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS );
		$files = new RecursiveIteratorIterator( $it, RecursiveIteratorIterator::CHILD_FIRST );
		foreach( $files as $file ){
			if( $file->getFilename() === '.' || $file->getFilename() === '..'){  continue;  }
			if( $file->isDir() ){
				rmdir( $file->getRealPath() );
			}else{
				unlink( $file->getRealPath() );
			}
		}
		rmdir( $dir );
		return true;
	}

}