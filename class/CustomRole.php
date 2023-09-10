<?php

namespace Wof;

class CustomRole
{

    /**
    * @var string
    */
    protected $name;

    /**
    * @var string
    */
    protected $label;

    protected $capabilities = [
        'publish_posts' => true,
        'edit_posts' => true,
        'delete_post' => true,
        'delete_posts' => true,
        'edit_published_posts' => true,
        'delete_published_posts' => true,
        'upload_files' => true,
        'read' => true,
    ];


    public function __construct($name, $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public function setCapability($capabilityName, $value)
    {
        $this->capabilities[$capabilityName] = $value;
        return $this;
    }

    public function delete()
    {
        //DOC https://developer.wordpress.org/reference/functions/remove_role/
        remove_role($this->name);
    }

    // =======================================================================
    public function register()
    {
        add_role(
            $this->name,
            $this->label,
            $this->capabilities
        );
    }
}
