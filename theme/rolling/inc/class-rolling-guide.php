<?php
/**
 * Page « Apparence > Guide des styles Rolling » : chaque classe et chaque variable avec son rôle.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Guide des styles.
 */
class Rolling_Guide {

	/**
	 * Branche les crochets.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
	}

	/**
	 * Ajoute la page sous Apparence.
	 */
	public static function menu() {
		add_theme_page(
			__( 'Guide des styles Rolling', 'rolling' ),
			__( 'Guide des styles Rolling', 'rolling' ),
			'edit_theme_options',
			'rolling-guide',
			array( __CLASS__, 'afficher' )
		);
	}

	/**
	 * Affiche le guide.
	 */
	public static function afficher() {
		$classes    = Rolling_Donnees::lire( 'classes' );
		$variables  = Rolling_Donnees::lire( 'variables' );
		$par_groupe = array();

		foreach ( $classes['classes'] ?? array() as $classe ) {
			$par_groupe[ $classe['groupe'] ][] = $classe;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Guide des styles Rolling', 'rolling' ); ?></h1>
			<p><?php esc_html_e( 'Dans Elementor, sélectionnez un élément, puis ajoutez une classe dans l’onglet Style, champ « Classes ». Chaque classe a un seul rôle. Les classes « À ajouter à… » se combinent avec une autre.', 'rolling' ); ?></p>
			<p><?php esc_html_e( 'Dans un titre : italique = mot en dégradé de la section, gras = dégradé IA (bleu), souligné = dégradé humain (orange). Dans un chapô : souligné = mot écrit et souligné à la main.', 'rolling' ); ?></p>
			<?php foreach ( $classes['groupes'] ?? array() as $groupe ) : ?>
				<?php if ( empty( $par_groupe[ $groupe ] ) ) : ?>
					<?php continue; ?>
				<?php endif; ?>
				<h2><?php echo esc_html( $groupe ); ?></h2>
				<table class="widefat striped">
					<thead><tr><th scope="col"><?php esc_html_e( 'Classe', 'rolling' ); ?></th><th scope="col"><?php esc_html_e( 'Rôle', 'rolling' ); ?></th></tr></thead>
					<tbody>
						<?php foreach ( $par_groupe[ $groupe ] as $classe ) : ?>
							<tr><td><code><?php echo esc_html( $classe['label'] ); ?></code></td><td><?php echo esc_html( $classe['description'] ); ?></td></tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endforeach; ?>
			<h2><?php esc_html_e( 'Variables', 'rolling' ); ?></h2>
			<table class="widefat striped">
				<thead><tr><th scope="col"><?php esc_html_e( 'Variable', 'rolling' ); ?></th><th scope="col"><?php esc_html_e( 'Valeur', 'rolling' ); ?></th><th scope="col"><?php esc_html_e( 'Rôle', 'rolling' ); ?></th></tr></thead>
				<tbody>
					<?php foreach ( $variables as $variable ) : ?>
						<tr>
							<td><code><?php echo esc_html( $variable['label'] ); ?></code></td>
							<td><?php echo esc_html( (string) ( $variable['value']['value'] ?? '' ) ); ?></td>
							<td><?php echo esc_html( $variable['description'] ?? '' ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
