<?php namespace RedFox\Entity\Attachment;

use Eternity\ServiceManager\ServiceContainer;
use RedFox\Entity\Entity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * Class AttachmentManager
 * @package RedFox\Entity\Attachment
 * @property-read \RedFox\Entity\Attachment\Attachment[] $files
 * @property-read \RedFox\Entity\Attachment\Attachment $first
 * @property-read string $name
 */
class AttachmentManager {

	/** @var  \RedFox\Entity\Attachment\Attachment[] */
	protected $attachments = null;

	/** @var AttachmentConfigInterface */
	protected $config;

	protected $path;
	protected $pathId;
	protected $urlBase;
	protected $owner;
	protected $descriptor;

	public function getPath(): string { return $this->path; }
	public function getDescriptor(): string { return $this->descriptor; }
	public function getPathId(): string { return $this->pathId; }
	public function getUrlBase(): string { return $this->urlBase; }
	public function getOwner(): string { return $this->owner; }

	/**
	 * @return \RedFox\Entity\Attachment\Attachment[]
	 */
	public function getAttachments() {
		if (is_null($this->attachments)) $this->collect();
		return $this->attachments;
	}

	/**
	 * @param $filename
	 * @return null|Attachment
	 */
	public function getAttachment($filename) {
		$attachments = $this->getAttachments();
		foreach ($attachments as $attachment) {
			if ($attachment->getFilename() === $filename) return $attachment;
		}
		return null;
	}

	public function getAttachmentCount() {
		return count($this->getAttachments());
	}

	public function hasAttachments() {
		return (bool)count($this->getAttachments());
	}


	public function __construct(Entity $owner, AttachmentDescriptor $descriptor) {
		$this->config = ServiceContainer::get(AttachmentConfigInterface::class);
		$this->owner = $owner;
		$this->descriptor = $descriptor;
		$ownerId = $owner->id ? $owner->id : '0';
		$this->path = $this->config::attachments_path() . $descriptor->getEntityShortName() . '/' . $ownerId . '/' . $descriptor->getName() . '/';
		$this->pathId = $descriptor->getEntityShortName() . '-' . $ownerId . '-' . $descriptor->getName();
		$this->urlBase = $this->config::attachments_url() . $descriptor->getEntityShortName() . '/' . $ownerId . '/' . $descriptor->getName() . '/';
		if (!is_dir($this->path)) {
			mkdir($this->path, 0777, true);
		}
	}

	public function uploadFile(UploadedFile $file, $replace = false) {
		if ($this->getAttachmentCount() >= $this->descriptor->getMaxFileCount() && !$replace) {
			throw new Exception("Max number of files: " . $this->descriptor->getMaxFileCount(), Exception::FILE_COUNT_ERROR);
		} else if ($file->getClientSize() > $this->descriptor->getMaxFileSize()) {
			throw new Exception("Max size of files: " . $this->descriptor->getMaxFileSize() . 'bytes', Exception::FILE_SIZE_ERROR);
		} else if (is_array($this->descriptor->getAcceptedExtensions()) && !in_array($file->getClientOriginalExtension(), $this->descriptor->getAcceptedExtensions())) {
			throw new Exception("Acceptable filetypes are: " . join(', ', $this->descriptor->getAcceptedExtensions()), Exception::FILE_TYPE_ERROR);
		} else {
			if ($this->descriptor->getMaxFileCount() == 1 && $this->getAttachmentCount() == 1 && $replace) {
				$this->first->delete();
			}
			$file->move($this->path, $file->getClientOriginalName());
			$this->owner->attachmentAdded($this, $file->getClientOriginalName());
			$this->attachments = null;
			return true;
		}
	}

	public function addFile(File $file, $replace = false) {
		if ($this->getAttachmentCount() >= $this->descriptor->getMaxFileCount() && !$replace) {
			throw new Exception("Too many files", Exception::FILE_COUNT_ERROR);
		} else if ($file->getSize() > $this->descriptor->getMaxFileSize()) {
			throw new Exception("Too big file", Exception::FILE_SIZE_ERROR);
		} else if (is_array($this->descriptor->getAcceptedExtensions()) && !in_array($file->getExtension(), $this->descriptor->getAcceptedExtensions())) {
			throw new Exception("File type is not accepted", Exception::FILE_TYPE_ERROR);
		} else {
			if ($this->descriptor->getMaxFileCount() == 1 && $this->getAttachmentCount() == 1 && $replace) {
				$this->first->delete();
			}
			copy($file->getPath() . '/' . $file->getFilename(), $this->path . $file->getFilename());
			$this->owner->attachmentAdded($this, $file->getFilename());
			$this->attachments = null;
			return true;
		}
	}

	//public function copyAttachment(Attachment $attachment) {
	//	//TODO: should check like upload
	//	copy($attachment->getFile(), $this->path.$attachment->getFilename());
	//	$this->attachments = null;
	//}


	public function deleteFile($filename) {
		$attachments = $this->getAttachments();
		if (array_key_exists($filename, $attachments)) {
			$attachments[$filename]->delete();
			unset($attachments[$filename]);
		}
	}

	protected function collect() {
		$files = glob($this->getPath() . '/*');
		$attachments = [];
		foreach ($files as $file) {
			$attachment = new Attachment($file, $this);
			$attachments[$attachment->getFilename()] = $attachment;
		}
		$this->attachments = $attachments;
		return $attachments;
	}

	public function __get($name) {
		$attachments = $this->getAttachments();
		switch ($name) {
			case 'name':
				return $this->descriptor->getName();
				break;
			case 'files':
				return $attachments;
				break;
			case 'first':
				if ($this->hasAttachments()) return reset($attachments);
				else return null;
				break;
		}
	}

	public function __isset($name) {
		return in_array($name, ['files', 'first']);
	}


}

