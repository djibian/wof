<?php
namespace Wof;

class TaxonomyCustomMetadata
{
    // sur quelle taxonomy nous ajoutons une métadata
    protected $taxonomy;
    // identifiant de la metadata
    protected $keyName;
    // étiquette de la metadata qui sera affichée dans l'interface
    protected $label;

    public function __construct($keyName, $label, $taxonomy)
    {
        $this->taxonomy = $taxonomy;
        $this->keyName = $keyName;
        $this->label = $label;
    }

    public function register()
    {
        // Ajout de la metadata dans les formulaires
        add_action($this->taxonomy . '_add_form_fields', [$this, 'displayAddForm']);
        add_action($this->taxonomy . '_edit_form_fields', [$this, 'displayEditForm']);

        // Ajout de la colonne pour cette metadata dans la liste des taxonomies
		add_filter( 'manage_edit-' . $this->taxonomy . '_columns', array( $this, 'addMetadataColumn' ) );
		add_filter( 'manage_' . $this->taxonomy . '_custom_column', array( $this, 'addMetadataValuesInColumn' ), 10, 3 );

        // Sauvegarde de la metadata
        add_action( 'created_' . $this->taxonomy, [$this, 'save'] );
        add_action( 'edited_' . $this->taxonomy, [$this, 'save'] );
    }


    /**
	 * Ajout d'une colonne dans la liste des éléments de la taxonomie sur laquelle porte la metadata juste avant la colonne "Total" (dernière colonne)
	 *
	 * @param mixed $columns Columns array.
	 * @return array
	 */
	public function addMetadataColumn( $columns ) {
		$new_columns = array();
		$new_columns[$this->keyName] = $this->label;
		if ( isset( $columns['posts'] ) ) {
			$new_columns['posts'] = $columns['posts'];
			unset( $columns['posts'] );
		}
		$columns = array_merge( $columns, $new_columns );
		return $columns;
	}

    /**
	 * Ajout des valeurs de la colonne de notre metadata dans la liste des éléments de la taxonomie sur laquelle porte la metadata
	 *
	 * @param string $columns Column HTML output.
	 * @param string $column Column name.
	 * @param int    $id Product ID.
	 *
	 * @return string
	 */
    public function addMetadataValuesInColumn( $columns, $column, $id ) {
		if ( $this->keyName === $column ) {
			$metadataValue = get_term_meta( $id, $this->keyName, true );
			$columns .= '<p>' . $metadataValue . '</p>';
		}
		return $columns;
	}

    /**
     *
	 * Affichage de notre partie de formulaire correspondant à la metadata lors de l'ajout d'un élément à la taxonomie
	 *
	 *
	 */
    public function displayAddForm( $taxonomy ) {
		?>
            <div class="form-field term-name-wrap">
                <label for="<?php esc_html_e($this->keyName)?>"><?php esc_html_e($this->label)?></label>
                <input type="text" name="<?php esc_html_e($this->keyName)?>" id="<?php esc_html_e($this->keyName)?>" />
            </div>
		<?php
    }

    public function save($taxonomyId)
    {
        $value = filter_input(INPUT_POST, $this->keyName);
        // si on est dans le quick edit (modification rapide) alors $value sera vide et on écrase l'image si on fait l'update !
        if ( isset( $value )){
            $this->setValue($value, $taxonomyId);
        }
    }

    public function setValue($value, $taxonomyId){
        // DOC https://developer.wordpress.org/reference/functions/update_term_meta/
        update_term_meta(
            $taxonomyId,    // sur quelle taxonomie nous ajoutons une metadata
            $this->keyName,    // nom de la metadata
            $value
        );
    }

    public function getValue($taxonomyId)
    {
        // récupération de la valeur de la metadata
        // DOC https://developer.wordpress.org/reference/functions/get_term_meta/
        $value = get_term_meta(
            $taxonomyId,
            $this->keyName,
            true    //Whether to return a single value. This parameter has no effect if $key is not specified.
        );

        return $value;
    }

    /**
     *
	 * Affichage de notre partie de formulaire correspondant à la metadata lors de l'édition d'un élément de la taxonomie
	 *
	 *
	 */
    public function displayEditForm($taxonomy)
    {
        $value = $this->getValue($taxonomy->term_id);
		?>
			<tr class="form-field term-name-wrap">
			    <th scope="row"><label for="<?php esc_html_e($this->keyName)?>"><?php esc_html_e($this->label)?></label></th>
			    <td>
				    <input name="<?php esc_html_e($this->keyName)?>" id="<?php esc_html_e($this->keyName)?>" type="text" value="<?php esc_html_e($value)?>" size="40" aria-required="true">
			    </td>
			</tr>
		<?php
    }
}
