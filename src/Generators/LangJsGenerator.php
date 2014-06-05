<?php namespace Mariuzzo\LaravelJsLocalization\Generators;

use Illuminate\Filesystem\Filesystem as File;
use JShrink\Minifier;

class LangJsGenerator
{
    protected $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function make($target, $options)
    {
        $messages = $this->getMessages();
        $this->prepareTarget($target);

        $template = $this->file->get(__DIR__ . '/Templates/langjs_with_messages.js');
        $langjs = $this->file->get(__DIR__ . '/../../js/lang.js');

        $template = str_replace('\'{ messages }\'', json_encode($messages), $template);
        $template = str_replace('\'{ langjs }\';', $langjs, $template);

        if ($options['compress'])
        {
            $template = Minifier::minify($template);
        }

        return $this->file->put($target, $template);
    }

    protected function getMessages()
    {
	    $messages = [];
	    $path = app_path() . '/lang';

	    if (!$this->file->exists($path))
	    {
		    throw new \Exception("${path} doesn't exists!");
	    }

	    $messageKeys = \Config::get('js-localization::config.messages');

	    $langs = $this->file->directories($path);
	    foreach ($langs as $lang)
	    {
		    $lang = basename($lang);
		    foreach($messageKeys as $key)
		    {
			    if(\Lang::has($key, $lang))
			    {
				    $jsKey = $lang . '.' . $key;
				    $messages[$jsKey] = \Lang::get($key, [], $lang);
			    }
		    }
	    }

	    return $messages;
    }

    protected function prepareTarget($target)
    {
        $dirname = dirname($target);

        if (!$this->file->exists($dirname))
        {
            $this->file->makeDirectory($dirname);
        }
    }
}
