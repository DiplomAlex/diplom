<?php

class Model_Object_Site extends Model_Object_Abstract
{



	public function init()
	{
        $this->addElements(array(
            'id',
            'status',
            'host',
            'base_url',
            'vertical_id',
            'vertical_skin',
            'title',
            'brief',
            'full',
            'html_title',
            'meta_keywords',
            'meta_description',
            'is_linked_by_default',
            'default_language_id',
        ));
	}

	public function getSkin()
	{
		return $this->vertical_skin;
	}

    public function getSpecification()
    {
        $host = trim($this->host, '/');
        $s = $host;
        $baseUrl = trim($this->base_url, '/'); 
        $baseUrl = trim($baseUrl);
        if ( ! empty($baseUrl)) {
            $s .= '/'. $baseUrl;
        }
        return $s;
    }

}