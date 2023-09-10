<?php
namespace Wof;

class PostTypeCustomMetadata
{
    // sur quelle posttype nous ajoutons une métadata
    protected $customPostType;
    // identifiant de la metadata
    protected $keyName;
    // étiquette de la metadata qui sera affichée dans l'interface
    protected $label;

    public function __construct($keyName, $label, $customPostType)
    {
        $this->keyName = $keyName;
        $this->label = $label;
        $this->customPostType = $customPostType;
    }

    public function register()
    {
        // Ajout de la metadata dans les formulaires
        add_action('edit_form_after_editor', [$this, 'displayEditForm']);

        // Sauvegarde de la metadata
        add_action('save_post_' . $this->customPostType, [$this, 'save']);
    }

    public function getValue($postId)
    {
        // récupération de la valeur de la metadata
        $values = get_post_meta(
            $postId,
            $this->keyName,
        );

        if(!empty($values)) {
            // attention wp renvoie un tableau lorsque l'on accède à la valeur d'une métadata
            $value = $values[0];
        }
        else {
            $value = '';
        }

        return $value;
    }

    public function displayEditForm($post)
    {
        
        if($post->post_type !== $this->customPostType) {
            return false;
        }

        $value = $this->getValue($post->ID);
        
        echo '
            <div class="form-field">
                <label for="' . $this->keyName . '">' . $this->label . '</label>
                <input type="text" name="' . $this->keyName . '" id="' . $this->keyName . '" value="' . $value . '"/>
            </div>
        ';
    }

    public function save($postId)
    {
        // récupération de la valeur envoyée dans le formulaire
        $value = filter_input(INPUT_POST, $this->keyName);
        // enregistrement de la valeur en BDD
        if ( isset( $value )){
            $this->setValue($value, $postId);
        }
    }

    public function setValue($value, $postId)
    {
        // DOC https://developer.wordpress.org/reference/functions/update_post_meta/
        update_post_meta(
            $postId,
            $this->keyName,
            $value
        );
    }
}
