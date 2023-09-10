<?php

namespace Wof;

class PostTypeCustomMetabox
{
    // sur quelle post type nous ajoutons une metabox
    protected $postType;
    // identifiant de la metadata
    protected $keyName;
    // étiquette de la metabox qui sera affichée dans l'interface
    protected $label;
    // position dans la page d'édition
    protected $displayLocation;

    public function __construct($keyName, $label, $postType, $displaylocation = ['context' => 'side', 'priority' => 'default'])
    {
        $this->keyName = $keyName;
        $this->label = $label;
        $this->postType = $postType;
        $this->displayLocation = $displaylocation;
    }

    public function register()
    {
         /* Add meta boxes on the 'add_meta_boxes' hook. */
         add_action( 'add_meta_boxes', [$this, 'addMetaboxesToPostType'] );

         /* Save post meta on the 'save_post' hook. */
         //add_action( 'save_post', [$this, 'smashing_save_post_class_meta'], 10, 2 );

        // Sauvegarde de la metadata
        add_action('save_post_' . $this->postType, [$this, 'saveMetadata']);
    }


    /* Création d'une metabox à afficher dans l'écran d'édition du post type (post editor screen). */
    public function addMetaboxesToPostType()
    {
        add_meta_box(
            'wof_post' . $this->keyName . 'div',      // Identifiant unique de la metabox
            esc_html__( $this->label, 'wof' ),    // Etiquette de la metabox
            [$this, 'displayPostTypeMetabox'],   // Callback function pour afficher le contenu de la metabox
            $this->postType,         // page, post ou un custom post type
            $this->displayLocation['context'],         // Context : normal , side , advanced
            $this->displayLocation['priority']         // Priority : high, core, default, low
        );
    }

    /* Display the post meta box. */
    public function displayPostTypeMetabox( $post )
    {
    ?>
        <!-- creating nonce (number used once) : technique pour sécuriser l'utilisation des données du formulaire */-->
        <?php wp_nonce_field( $this->keyName . '_wof', $this->keyName . '_nonce' ); ?>

        <p>
        <label for="post<?php echo $this->keyName; ?>div"><?php _e( "Edit your text", 'wof' ); ?></label>
        <br />
        <input class="widefat" type="text" name="<?php echo $this->keyName; ?>" id="post<?php echo $this->keyName; ?>div" value="<?php echo esc_attr( $this->getValue( $post->ID ) ); ?>" size="30" />
        </p>
        
    <?php
    }


	/* Save the meta box’s post metadata. */
	public function saveMetadata( $post_id ) {

		/* Verify the nonce (number used once) before proceeding : technique pour sécuriser l'utilisation des données du formulaire */
        $nonce = filter_input(INPUT_POST, $this->keyName . '_nonce');
        if ( !isset( $nonce ) || !wp_verify_nonce( $nonce, $this->keyName . '_wof' ) )
		{
            return $post_id;
        }

		/* Get the post type object. */
		$post_type = get_post_type_object( $this->postType );
		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        {
		    return $post_id;
        }
	
        // récupération de la metadata et mise à jour si nécessaire
        $newMetaValue = filter_input(INPUT_POST, $this->keyName);
        if (isset($newMetaValue))
        {
            $this->setValue($post_id, $newMetaValue);
        }
    }

    public function getValue($postId)
    {
        //$class = explode('\\', get_class($this));
        // récupération de la valeur de la metadata
        $value = get_post_meta(
            $postId,
            $this->keyName/* . end($class)*/,
            true // important pour spécifier que l'on souhaite une unique valeur et non un tableau. La valeur par défaut est à false
        );
        return $value;
    }

    public function setValue($postId, $value)
    {
        // DOC https://developer.wordpress.org/reference/functions/update_post_meta/
        // si la metadata n'existe pas elle est créée
        //$class = explode('\\', get_class($this));
		update_post_meta( $postId, $this->keyName/* . end($class)*/, $value );
    }

}




    