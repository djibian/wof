<?php

namespace Wof;

// https://wpsmith.net/2019/custom-rewrite-rules-custom-post-types-taxonomies/
// https://developer.wordpress.org/reference/functions/register_post_type/
class CustomPostType
{
    protected $key;
    protected $label;
    protected $labels;

    private $defaultOptions = [
        'label' => 'Custom post type Name',
        // Modification des labels par défaut dans toute l'interface utilisateur
        'labels' => [],
        'description' => 'This is a custom post type',
        // le custom post type sera éditable depuis le bo
        'public' => true,
        // est ce que les contenus gèrent le fait qu'ils ont un parent
        'hierarchical' => false,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        //'show_ui' => true, // public par défaut
        //'show_in_menu' => true, // public par défaut
        //'show_in_nav_menus' => true, // public par défaut
        //'show_in_admin_bar' => true, // public par défaut
        // attention active Gutenberg  ! ; il faudra que l'on gère la désactivation de gutenberg manuellement
        'show_in_rest' => true,
        //'rest_base' => $key, // $post_type par défaut
        //'rest_controller_class' => 'WP_REST_Posts_Controller',
        
        /*
        Positions for Core Menu Items
        2 Dashboard
        4 Separator
        5 Posts
        10 Media
        15 Links
        20 Pages
        25 Comments
        59 Separator
        60 Appearance
        65 Plugins
        70 Users
        75 Tools
        80 Settings
        99 Separator
        */
        'menu_position' => 4,
        // Liste des icones : https://developer.wordpress.org/resource/dashicons/#menu
        'menu_icon' => 'dashicons-pets',
        // cet index permet de gérer les droits (acl)
        // lorsque la valeur vaut post ; le "cpt" utilisera les même droits que ceux appliqués sur la gestion des "posts"
        //'capability_type' => 'post',
        //'capabilities' => [],
        // attention à ne surtout pas oublier cette ligne ; si custom capabilities + Gutenberg
        // 'map_meta_cap' => true,
        'supports' => [
            'title',
            'editor', // il s'agit du contenu
            // 'comments',
            // 'revisions',
            // 'trackbacks',
            // 'author',
            // 'excerpt',
            // 'page-attributes',
            'thumbnail',
            // 'custom-fields',
            // 'post-formats' // https://wordpress.org/support/article/post-formats/
        ],
        //'register_meta_box_cb' = null,
        //'taxonomies' = [],
        'has_archive' => true,
        //'rewrite' => false,
        //'query_var' => 'post', 
        'can_export' => true,
        'delete_with_user' => false, // pour ne pas mettre à la poubelle lorsque l'utilisateur qui a créée ce CPT est supprimé
        //'template' => [],
        //'template_lock' => false,
    ];

    protected $options = [];

    public function __construct($key, $label)
    {
        $this->key = $key;
        $this->label = $label;    
    }

    public function register()
    {
        // création du post type
        add_action('init', [$this, 'registerPostType']);

        // désactivation de gutenberg
        // 10 représente la priorité de la fonction; 10 souvent valeur par défaut
        // 2 représente le nombre de paramètre que wordpress va récupéré
        add_filter('use_block_editor_for_post_type', [$this, 'disableGutenberg'], 10, 2);

        // on donne à l'administateur les droits sur le custom post type
        add_action('admin_init', [$this, 'addCapabilitiesToAdmin']);


    }


    public function registerPostType()
    {
        // register_post_type est une méthode "native de wordpress
        // https://developer.wordpress.org/reference/functions/register_post_type/
        register_post_type($this->key, $this->getOptions());
    }


    public function addCapabilitiesToRole($roleName)
    {
        // récupération du rôle
        // https://developer.wordpress.org/reference/functions/get_role/
        $role = get_role( $roleName );

        // ajout des autorisations au rôle
        $role->add_cap( 'edit_' . $this->key);
        $role->add_cap( 'edit_' . $this->key . 's');
        $role->add_cap( 'read_' .  $this->key . 's' );

        $role->add_cap( 'delete_' .  $this->key);

        $role->add_cap( 'delete_' .  $this->key . 's' );
        $role->add_cap( 'delete_others_' .  $this->key . 's' );
        $role->add_cap( 'delete_published_' .  $this->key . 's' );

        $role->add_cap( 'edit_others' .  $this->key . 's' );
        $role->add_cap( 'publish_' .  $this->key . 's' );
        $role->add_cap( 'read_private_' .  $this->key . 's' );

    }

    public function addCapabilitiesToAdmin()
    {
        return $this->addCapabilitiesToRole('administrator');
    }

    public function getOptions()
    {
        $arguments = array_merge( $this->defaultOptions, $this->options );
        $arguments['label'] = $this->label;
        $arguments['labels'] = array(
            'name'                     => sprintf(/* translators: %s: Post type name */__( '%ss', 'wof'), $this->label),
            'menu_name'                => sprintf(/* translators: %s: Post type name */__( '%ss', 'wof'), $this->label),
            'singular_name'            => $this->label,
            'add_new'                  => __( 'Add New', 'wof'),
            'add_new_item'             => sprintf(/* translators: %s: Post type name */__( 'Add New %s', 'wof'), $this->label),/*Nouveau %s*/
            'edit_item'                => sprintf(/* translators: %s: Post type name */__( 'Edit %s', 'wof'), $this->label),/*Éditer %s*/
            'new_item'                 => sprintf(/* translators: %s: Post type name */__( 'New %s', 'wof'), $this->label),/*Nouveau %s*/
            'view_item'                => sprintf(/* translators: %s: Post type name */__( 'View %s', 'wof'), $this->label),/*Voir %s*/
            'view_items'               => sprintf(/* translators: %s: Post type name */__( 'View %ss', 'wof'), $this->label),/*Voir %ss*/
            'search_items'             => sprintf(/* translators: %s: Post type name */__( 'Search %ss', 'wof'), $this->label),/*Rechercher %ss*/
            'not_found'                => sprintf(/* translators: %s: Post type name */__( 'No %ss found.', 'wof'), $this->label),/*Aucun %s trouvé.*/
            'not_found_in_trash'       => sprintf(/* translators: %s: Post type name */__( 'No %ss found in Trash.', 'wof'), $this->label),/*Aucun %s trouvé dans la corbeille.*/
            'parent_item_colon'        => null,
            'all_items'                => sprintf(/* translators: %s: Post type name */__( 'All %ss', 'wof'), $this->label),/*Tous les %ss*/
            'archives'                 => sprintf(/* translators: %s: Post type name */__( '%s Archives', 'wof'), $this->label),/*Répertoire des %ss*/
            'attributes'               => sprintf(/* translators: %s: Post type name */__( '%s Attributs', 'wof'), $this->label),/*Caractéristiques des %ss*/
            'insert_into_item'         => sprintf(/* translators: %s: Post type name */__( 'Insert into %s', 'wof'), $this->label),/*Insérer dans %s*/
            'uploaded_to_this_item'    => sprintf(/* translators: %s: Post type name */__( 'Uploaded to this %s', 'wof'), $this->label),/*Télécharger vers %s*/
            //'featured_image'           => sprintf(/* translators: %s: Post type name */__( 'Featured image', 'wof'), $this->label),
            //'set_featured_image'       => sprintf(/* translators: %s: Post type name */__( 'Set featured image', 'wof'), $this->label),
            //'remove_featured_image'    => sprintf(/* translators: %s: Post type name */__( 'Remove featured image', 'wof'), $this->label),
            //'use_featured_image'       => sprintf(/* translators: %s: Post type name */__( 'Use as featured image', 'wof'), $this->label),
            'filter_items_list'        => sprintf(/* translators: %s: Post type name */__( 'Filter %ss list', 'wof'), $this->label),/*Filtrer la liste des %ss*/
            'items_list_navigation'    => sprintf(/* translators: %s: Post type name */__( '%ss list navigation', 'wof'), $this->label),/*Parcours des %ss*/
            'items_list'               => sprintf(/* translators: %s: Post type name */__( '%ss list', 'wof'), $this->label),/*Liste des %ss*/
            'item_published'           => sprintf(/* translators: %s: Post type name */__( '%s published.', 'wof'), $this->label),/*%s publié.*/
            'item_published_privately' => sprintf(/* translators: %s: Post type name */__( '%s published privately.', 'wof'), $this->label),/*%s publié en privé.*/
            'item_reverted_to_draft'   => sprintf(/* translators: %s: Post type name */__( '%s reverted to draft.', 'wof'), $this->label),/*%s rétabli comme brouilon.*/
            'item_scheduled'           => sprintf(/* translators: %s: Post type name */__( '%s scheduled.', 'wof'), $this->label),/*%s programmé.*/
            'item_updated'             => sprintf(/* translators: %s: Post type name */__( '%s updated.', 'wof'), $this->label),/*%s mis à jour.*/
        );
        if ($arguments['hierarchical']){
            $arguments['labels']['parent_item_colon'] = sprintf(/* translators: %s: Post type name */__( 'Parent %s:' ), $this->label)/*%s parent:*/;
        }

        // force le route pour l'api rest
        // $arguments['rewrite']['slug'] = $this->key;
        $arguments['capability_type'] =  $this->key;
        // à ne pas oublier si utilisation de gutenberg + custom capabilities ! ; permet à wp de faire la bonne association des droits entre les droits par défaut et les droits custom
        /*
        $arguments['capabilities'] = [
            'edit_post' => 'edit_' . $this->key,
            'edit_posts' => 'edit_' . $this->key .'s',
            'edit_others_posts' => 'edit_others_' .$this->key . 's',
            'publish_posts' => 'publish_' . $this->key . 's',
            'read_post' => 'read_' . $this->key,
            'read_private_posts' => 'read_private_' . $this->key . 's',
            'delete_post' => 'delete_' . $this->key
        ];
        */
        return $arguments;
    }

    public function disableGutenberg($isGutenbergEnable, $postType)
    {
        if($postType === $this->key) {
            return false;
        }
        else {
            return $isGutenbergEnable;
        }
    }

}