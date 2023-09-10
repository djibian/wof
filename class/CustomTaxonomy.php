<?php

namespace Wof;


class CustomTaxonomy
{

    protected $keyName;
    protected $label;

    // liste des postTypes sur lesquel la taxonomie est applicable
    protected $postTypes = [];

    private $defaultOptions = [
        'labels' => [],
        'description' => 'This is a custom Taxonomy',
        'public' => true,
        //'publicly_queryable'
        'hierarchical' => false,
        'show_ui' => true,
        //'show_in_menu'
        //'show_in_nav_menus' => false,
        'show_in_rest' => true,
        //'rest_base'
        //'rest_controller_class'
        //'show_tagcloud'
        //'show_in_quick_edit'
        'show_admin_column' => true,
        //'meta_box_cb'
        //'meta_box_sanitize_cb'
        //'capabilities'
        'rewrite' => [
            'slug' => ''
        ],
        'query_var' => true,
        //'update_count_callback'
        //'default_term'
        'sort' => true,
    ];

    protected $options = [];

    public function __construct($keyName, $label, array $postTypes)
    {
        $this->keyName = $keyName;
        $this->label = $label;
        $this->postTypes = $postTypes;
    }

    public function registerTaxonomy()
    {
        // DOC https://developer.wordpress.org/reference/functions/register_taxonomy/
        register_taxonomy(
            $this->keyName,
            $this->postTypes,
            $this->getOptions()
        );
    }

    // ===============================================================================================
    
    public function register()
    {
        add_action('init', [$this, 'registerTaxonomy']);
    }


    public function getOptions()
    {
        $arguments = array_merge( $this->defaultOptions, $this->options );
        $arguments['labels'] = array(
            'name'                       => sprintf(/* translators: %s: Post type name */__( '%ss', 'wof'), $this->label),/*%ss*/
            'menu_name'                  => sprintf(/* translators: %s: Post type name */__( '%ss', 'wof'), $this->label),/*%ss*/
            'singular_name'              => $this->label,
            'search_items'               => sprintf(/* translators: %s: Post type name */__( 'Search %ss', 'wof'), $this->label),/*Rechercher %ss*/
            'popular_items'              => sprintf(/* translators: %s: Post type name */__( 'Popular %ss', 'wof'), $this->label),/*%s populaires*/
            'all_items'                  => sprintf(/* translators: %s: Post type name */__( 'All %ss', 'wof'), $this->label),/*Tous les %ss*/
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => sprintf(/* translators: %s: Post type name */__( 'Edit %s', 'wof'), $this->label),/*Éditer %s*/
            'view_item'                  => sprintf(/* translators: %s: Post type name */__( 'View %s', 'wof'), $this->label),/*Voir %s*/
            'update_item'                => sprintf(/* translators: %s: Post type name */__( 'Update %s', 'wof'), $this->label),/*Mettre à jour %s*/
            'add_new_item'               => sprintf(/* translators: %s: Post type name */__( 'Add New %s', 'wof'), $this->label),/*Nouveau %s*/
            'new_item_name'              => sprintf(/* translators: %s: Post type name */__( 'New %s Name', 'wof'), $this->label),/*Nouveau nom pour %s*/
            'separate_items_with_commas' => sprintf(/* translators: %s: Post type name */__( 'Separate %ss with commas', 'wof'), $this->label),/*Séparer les %ss par une virgule*/
            'add_or_remove_items'        => sprintf(/* translators: %s: Post type name */__( 'Add or remove %ss', 'wof'), $this->label),/*Ajouter ou supprimer les %ss*/
            'choose_from_most_used'      => sprintf(/* translators: %s: Post type name */__( 'Choose from the most used %ss', 'wof'), $this->label),/*Choisir parmi les %ss les plus utlisés*/
            'not_found'                  => sprintf(/* translators: %s: Post type name */__( 'No %ss found', 'wof'), $this->label),/*Aucun %s trouvé*/
            'no_terms'                   => sprintf(/* translators: %s: Post type name */__( 'No %ss', 'wof'), $this->label),/*Aucun %s*/
            'filter_by_item'             => null,
            'items_list_navigation'      => sprintf(/* translators: %s: Post type name */__( '%s list navigation', 'wof'), $this->label),/*Parcours de la liste des %ss*/
            'items_list'                 => sprintf(/* translators: %s: Post type name */__( '%s list', 'wof'), $this->label),/*Liste des %ss*/
            /* translators: Tab heading when selecting from the most used terms. */
            'most_used'                  => __( 'Most used', 'wof'),/*Les plus utilisés*/
            'back_to_items'              => sprintf(/* translators: %s: Post type name */__( '&larr; Back to %ss', 'wof'), $this->label),/*&larr; Aller aux %ss*/
        );
        if ($arguments['hierarchical']){
            $arguments['labels']['popular_items'] = null;
            $arguments['labels']['parent_item'] = sprintf(/* translators: %s: Post type name */__( 'Parent %s' ), $this->label)/*%s parent*/;
            $arguments['labels']['parent_item_colon'] = sprintf(/* translators: %s: Post type name */__( 'Parent %s:' ), $this->label)/*%s parent:*/;
            $arguments['labels']['separate_items_with_commas'] = null;
            $arguments['labels']['add_or_remove_items'] = null;
            $arguments['labels']['choose_from_most_used'] = null;
            $arguments['labels']['filter_by_item'] = sprintf(/* translators: %s: Post type name */__( 'Filter by %s', 'wof'), $this->label)/*Filtrer par %s*/;
        }
        // force le route pour l'api rest
        // $arguments['rewrite']['slug'] = $this->key;
        return $arguments;
    }
}
