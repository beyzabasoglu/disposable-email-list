<?php

namespace ContentFarm\DisposableEmail;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;


class DisposableEmailService
{
    private $filesystem, $adapter = null;
    public $files, $domains,$exceptions = [];
    public $mail,$domain = null;
    public $lists = ['default'];

    public function __construct()
    {
        $this->adapter = new Local(__DIR__ . '/Lists');
        $this->filesystem = new Filesystem($this->adapter);

    }

    public function clear()
    {
        $this->mail = ltrim(rtrim($this->mail));

    }

    public function findDomain(){
        $domain = explode('@',$this->mail);
        $this->domain = $domain[1];

    }

    public function getDomain(){
        $this->findDomain();
        return $this->domain;
    }

    public function isDisposableEmail()
    {
        $this->clear();

        if (!filter_var($this->mail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $this->loadFiles();
        $this->findDomain();

        if(in_array($this->domain,$this->exceptions)){
            return false;
        }

        if(in_array($this->domain,$this->domains)){
            return true;
        }

        return false;

    }


    public function isValidMail()
    {
        $this->mail = filter_var($this->mail, FILTER_VALIDATE_EMAIL);
    }

    public function isDomainAlive()
    {
        putenv('RES_OPTIONS=retrans:1 retry:1 timeout:5 attempts:1');
        $this->domain = gethostbyname($this->domain);
    }

    public function loadFiles()
    {

        $this->findListFiles();

        foreach ($this->files as $file) {
            $stream = $this->filesystem->readStream($file['path']);
            $contents = stream_get_contents($stream);
            fclose($stream);
            foreach (preg_split("/((\r?\n)|(\r\n?))/", $contents) as $line) {
                $this->domains[] = $line;
            }

        }

        $this->domains = array_map("unserialize", array_unique(array_map("serialize", $this->domains)));

        return $this->domains;
    }

    public function findListFiles()
    {

        foreach ($this->lists as $list) {
            $contents = $this->filesystem->listContents('/' . $list);

            foreach ($contents as $object) {

                $this->files[] = $object;
            }
        }

    }
}
