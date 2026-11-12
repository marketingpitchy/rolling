<?php
/**
 * Chargement de page « effet film » : ouverture cinéma à l'arrivée sur le site (première page de la visite),
 * puis fine barre de lecture à chaque changement de page. Rien sur une landing, dans l'éditeur, ni avec
 * « réduire les animations ». Décidé dans le navigateur : compatible avec le cache des pages (WP Super Cache).
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

/**
 * Chargement de page.
 */
class Rolling_Chargement {

	/**
	 * Branche les crochets.
	 */
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'script_tete' ), 1 );
		add_action( 'wp_body_open', array( __CLASS__, 'ecran' ), 1 );
		add_action( 'customize_register', array( __CLASS__, 'personnaliser' ) );
	}

	/**
	 * L'animation peut-elle passer sur cette page ?
	 *
	 * @return bool
	 */
	private static function actif() {
		if ( is_admin() || is_customize_preview() || ! get_theme_mod( 'rolling_chargement', true ) || Rolling_Entete_Pied::est_landing() ) {
			return false;
		}
		if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return false;
		}

		return true;
	}

	/**
	 * Dans le <head>, avant l'affichage : ouverture (première page de la visite) ou barre (pages suivantes).
	 */
	public static function script_tete() {
		if ( ! self::actif() ) {
			return;
		}
		?>
<script>(function(){var d=document.documentElement;try{if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches){return;}if(sessionStorage.getItem('rl-intro-vue')){d.classList.add('rl-barre-joue');}else{sessionStorage.setItem('rl-intro-vue','1');d.classList.add('rl-intro-joue');}}catch(e){}})();</script>
		<?php
	}

	/**
	 * Juste après <body> : l'écran d'ouverture et la barre de lecture (masqués tant que le script ne les active pas).
	 */
	public static function ecran() {
		if ( ! self::actif() ) {
			return;
		}
		?>
<div class="rl-intro" aria-hidden="true">
	<div class="rl-intro__bande rl-intro__bande--haut"></div>
	<div class="rl-intro__bande rl-intro__bande--bas"></div>
	<div class="rl-intro__centre">
		<span class="rl-intro__logo"><?php echo Rolling_Entete_Pied::logo( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de wp_get_attachment_image(). ?></span>
		<span class="rl-intro__ligne"><span class="rl-intro__rec"><?php esc_html_e( 'Rec', 'rolling' ); ?></span><span class="rl-intro__tc">00:00:00:00</span></span>
		<span class="rl-intro__regle"><span>00:00:00</span><span>00:00:01</span><i class="rl-intro__trait"></i><i class="rl-intro__tete"></i></span>
	</div>
</div>
<div class="rl-barre-page" aria-hidden="true"></div>
<script>(function(){var d=document.documentElement,e=document.querySelector('.rl-intro');if(!e||!d.classList.contains('rl-intro-joue')){if(e){e.remove();}return;}var t=e.querySelector('.rl-intro__tc'),t0=performance.now();(function tic(n){var ms=Math.min(n-t0,700),f=Math.floor(ms/40);t.textContent='00:00:'+(ms>=700?'01:00':'00:'+('0'+f).slice(-2));if(ms<700){requestAnimationFrame(tic);}}(t0));function fin(){e.classList.add('rl-intro--passe');setTimeout(function(){e.remove();},260);document.removeEventListener('keydown',touche);}function touche(v){if(v.key==='Escape'){fin();}}e.addEventListener('click',fin);document.addEventListener('keydown',touche);setTimeout(function(){if(e.isConnected){e.remove();}},1700);})();</script>
		<?php
	}

	/**
	 * Personnaliser > Rolling : chargement de page (activé par défaut).
	 *
	 * @param WP_Customize_Manager $wp_customize Personnalisateur.
	 */
	public static function personnaliser( $wp_customize ) {
		$wp_customize->add_section(
			'rolling_chargement',
			array(
				'title'    => __( 'Rolling : chargement de page', 'rolling' ),
				'priority' => 168,
			)
		);
		$wp_customize->add_setting(
			'rolling_chargement',
			array(
				'default'           => true,
				'sanitize_callback' => 'rest_sanitize_boolean',
			)
		);
		$wp_customize->add_control(
			'rolling_chargement',
			array(
				'section'     => 'rolling_chargement',
				'type'        => 'checkbox',
				'label'       => __( 'Ouverture cinéma à l’arrivée sur le site, barre de lecture entre les pages', 'rolling' ),
				'description' => __( 'Jamais sur une landing, ni pour les visiteurs qui ont choisi de réduire les animations.', 'rolling' ),
			)
		);
	}
}
