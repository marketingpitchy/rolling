<?php
/**
 * Pied de page Rolling : présentation, menu principal, menu des informations. Sur une landing : version simple.
 *
 * @package Rolling
 */

defined( 'ABSPATH' ) || exit;

$rolling_menus = array(
	'principal' => Rolling_Entete_Pied::texte( 'rolling_pied_titre_navigation' ),
	'pied'      => Rolling_Entete_Pied::texte( 'rolling_pied_titre_informations' ),
);
?>
<?php if ( Rolling_Entete_Pied::est_landing() ) : ?>
<footer class="rl-pied rl-pied--simple">
	<div class="rl-pied__conteneur">
		<div class="rl-pied__bas">
			<span class="rl-pied__logo"><?php echo Rolling_Entete_Pied::logo( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de wp_get_attachment_image(). ?></span>
			<nav aria-label="<?php echo esc_attr( (string) wp_get_nav_menu_name( 'pied' ) ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'pied',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		</div>
	</div>
</footer>
<?php else : ?>
<footer class="rl-pied">
	<div class="rl-pied__conteneur">
		<div class="rl-pied__haut">
			<div>
				<a class="rl-pied__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo Rolling_Entete_Pied::logo( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de wp_get_attachment_image(). ?></a>
				<?php if ( Rolling_Entete_Pied::texte( 'rolling_pied_presentation' ) ) : ?>
					<p class="rl-pied__presentation"><?php echo esc_html( Rolling_Entete_Pied::texte( 'rolling_pied_presentation' ) ); ?></p>
				<?php endif; ?>
				<?php if ( Rolling_Entete_Pied::texte( 'rolling_pied_marque' ) ) : ?>
					<p class="rl-pied__marque"><?php echo esc_html( Rolling_Entete_Pied::texte( 'rolling_pied_marque' ) ); ?></p>
				<?php endif; ?>
			</div>
			<?php foreach ( $rolling_menus as $rolling_emplacement => $rolling_titre ) : ?>
				<?php if ( has_nav_menu( $rolling_emplacement ) ) : ?>
					<nav aria-label="<?php echo esc_attr( $rolling_titre ? $rolling_titre : (string) wp_get_nav_menu_name( $rolling_emplacement ) ); ?>">
						<?php if ( $rolling_titre ) : ?>
							<p class="rl-pied__titre"><?php echo esc_html( $rolling_titre ); ?></p>
						<?php endif; ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => $rolling_emplacement,
								'container'      => false,
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
						?>
					</nav>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<div class="rl-pied__bas">
			<span><?php echo esc_html( Rolling_Entete_Pied::texte( 'rolling_pied_copyright' ) ); ?></span>
			<span><?php echo esc_html( Rolling_Entete_Pied::texte( 'rolling_pied_mention' ) ); ?></span>
		</div>
	</div>
</footer>
<?php endif; ?>
