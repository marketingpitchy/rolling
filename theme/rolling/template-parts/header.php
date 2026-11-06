<?php
/**
 * En-tête Rolling : logo, menu principal, bouton. Sur une landing : logo seul.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

$rolling_landing = Rolling_Entete_Pied::est_landing();
$rolling_fleche  = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>';
?>
<header class="rl-entete<?php echo $rolling_landing ? ' rl-entete--logo-seul' : ''; ?>">
	<div class="rl-entete__barre">
		<?php if ( $rolling_landing ) : ?>
			<span class="rl-entete__logo"><?php echo Rolling_Entete_Pied::logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de wp_get_attachment_image(). ?></span>
		<?php else : ?>
			<a class="rl-entete__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo Rolling_Entete_Pied::logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de wp_get_attachment_image(). ?></a>
			<nav class="rl-entete__nav" id="rl-navigation" aria-label="<?php echo esc_attr( (string) wp_get_nav_menu_name( 'principal' ) ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'principal',
						'container'      => false,
						'menu_class'     => 'rl-menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
				<?php if ( Rolling_Entete_Pied::texte( 'rolling_cta_texte' ) ) : ?>
					<a class="rl-bouton" href="<?php echo esc_url( Rolling_Entete_Pied::lien_bouton() ); ?>"><?php echo esc_html( Rolling_Entete_Pied::texte( 'rolling_cta_texte' ) ); ?> <?php echo $rolling_fleche; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG décoratif fixe. ?></a>
				<?php endif; ?>
			</nav>
			<button class="rl-burger" type="button" aria-expanded="false" aria-controls="rl-navigation" aria-label="<?php echo esc_attr( Rolling_Entete_Pied::texte( 'rolling_libelle_menu_ouvrir' ) ); ?>" data-ouvrir="<?php echo esc_attr( Rolling_Entete_Pied::texte( 'rolling_libelle_menu_ouvrir' ) ); ?>" data-fermer="<?php echo esc_attr( Rolling_Entete_Pied::texte( 'rolling_libelle_menu_fermer' ) ); ?>">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
			</button>
		<?php endif; ?>
	</div>
</header>
