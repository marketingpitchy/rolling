<?php
/**
 * Pied de page Rolling : logo, réseaux, encart « Réserver un échange », colonnes de liens (menu « Pied de page :
 * colonnes »), badges de confiance, avis clients facultatifs, mentions légales. Sur une landing : version simple.
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
	<?php
	$rolling_reseaux = array(
		'linkedin'  => array( 'LinkedIn', '<path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3zM9 9h3.8v1.7h.05c.53-1 1.84-2.05 3.78-2.05C20.6 8.65 21 11.2 21 14.5V21h-4v-5.8c0-1.4 0-3.2-1.95-3.2S12.8 13.5 12.8 15.1V21H9z" fill="currentColor"/>' ),
		'youtube'   => array( 'YouTube', '<path d="M22 8.2a3 3 0 0 0-2.1-2.1C18 5.6 12 5.6 12 5.6s-6 0-7.9.5A3 3 0 0 0 2 8.2 31 31 0 0 0 1.6 12a31 31 0 0 0 .4 3.8 3 3 0 0 0 2.1 2.1c1.9.5 7.9.5 7.9.5s6 0 7.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .4-3.8 31 31 0 0 0-.4-3.8zM10 15V9l5.2 3z" fill="currentColor"/>' ),
		'instagram' => array( 'Instagram', '<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor"/>' ),
	);
	$rolling_badges  = array(
		'rolling_badge_1' => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
		'rolling_badge_2' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
		'rolling_badge_3' => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/>',
	);
	$rolling_fleche  = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>';
	$rolling_avis    = Rolling_Entete_Pied::avis();
	?>
<footer class="rl-pied">
	<div class="rl-pied__conteneur">
		<div class="rl-pied__tete">
			<div class="rl-pied__marque-bloc">
				<a class="rl-pied__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo Rolling_Entete_Pied::logo( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML de wp_get_attachment_image(). ?></a>
				<?php if ( Rolling_Entete_Pied::texte( 'rolling_pied_presentation' ) ) : ?>
					<p class="rl-pied__presentation"><?php echo esc_html( trim( Rolling_Entete_Pied::texte( 'rolling_pied_presentation' ) . ' ' . Rolling_Entete_Pied::texte( 'rolling_pied_marque' ) ) ); ?></p>
				<?php endif; ?>
				<ul class="rl-pied__reseaux">
					<?php foreach ( $rolling_reseaux as $rolling_cle => $rolling_reseau ) : ?>
						<?php if ( get_theme_mod( 'rolling_reseau_' . $rolling_cle ) ) : ?>
							<li><a class="rl-pied__reseau" href="<?php echo esc_url( get_theme_mod( 'rolling_reseau_' . $rolling_cle ) ); ?>" target="_blank" rel="noopener me">
								<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><?php echo $rolling_reseau[1]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icône SVG fixe. ?></svg>
								<span class="screen-reader-text"><?php echo esc_html( sprintf( /* translators: %s : réseau social. */ __( 'Rolling sur %s (nouvel onglet)', 'rolling' ), $rolling_reseau[0] ) ); ?></span>
							</a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php if ( Rolling_Entete_Pied::texte( 'rolling_pied_appel' ) && Rolling_Entete_Pied::texte( 'rolling_cta_texte' ) ) : ?>
				<div class="rl-pied__appel">
					<p class="rl-pied__appel-titre"><?php echo esc_html( Rolling_Entete_Pied::texte( 'rolling_pied_appel' ) ); ?></p>
					<a class="rl-pied__bouton" href="<?php echo esc_url( Rolling_Entete_Pied::lien_bouton() ); ?>"><?php echo esc_html( Rolling_Entete_Pied::texte( 'rolling_cta_texte' ) ); ?> <?php echo $rolling_fleche; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG décoratif fixe. ?></a>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( has_nav_menu( 'colonnes' ) ) : ?>
			<nav class="rl-pied__colonnes" aria-label="<?php echo esc_attr( (string) wp_get_nav_menu_name( 'colonnes' ) ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'colonnes',
						'container'      => false,
						'menu_class'     => 'rl-pied__liste-colonnes',
						'depth'          => 2,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php else : ?>
			<div class="rl-pied__colonnes rl-pied__colonnes--simple">
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
		<?php endif; ?>

		<div class="rl-pied__confiance">
			<ul class="rl-pied__badges">
				<?php foreach ( $rolling_badges as $rolling_cle => $rolling_icone ) : ?>
					<?php if ( Rolling_Entete_Pied::texte( $rolling_cle ) ) : ?>
						<li class="rl-badge"><span class="rl-badge__icone" aria-hidden="true"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false"><?php echo $rolling_icone; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icône SVG fixe. ?></svg></span><?php echo esc_html( Rolling_Entete_Pied::texte( $rolling_cle ) ); ?></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<?php if ( $rolling_avis ) : ?>
				<ul class="rl-pied__avis" aria-label="<?php esc_attr_e( 'Avis de nos clients', 'rolling' ); ?>">
					<?php foreach ( $rolling_avis as $rolling_un_avis ) : ?>
						<li>
							<?php echo $rolling_un_avis['lien'] ? '<a class="rl-avis" href="' . esc_url( $rolling_un_avis['lien'] ) . '" target="_blank" rel="noopener">' : '<span class="rl-avis">'; ?>
								<?php if ( $rolling_un_avis['image'] ) : ?>
									<span class="rl-avis__image"><?php echo wp_get_attachment_image( $rolling_un_avis['image'], 'thumbnail', false, array( 'alt' => '' ) ); ?></span>
								<?php endif; ?>
								<span class="rl-avis__texte">
									<?php if ( $rolling_un_avis['note'] ) : ?>
										<?php echo Rolling_Entete_Pied::etoiles( $rolling_un_avis['note'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG construit et échappé. ?>
										<span><strong><?php echo esc_html( $rolling_un_avis['note'] ); ?>/5</strong><?php echo $rolling_un_avis['texte'] ? ' · ' . esc_html( $rolling_un_avis['texte'] ) : ''; ?></span>
									<?php else : ?>
										<span><?php echo esc_html( $rolling_un_avis['texte'] ); ?></span>
									<?php endif; ?>
								</span>
								<?php if ( $rolling_un_avis['lien'] ) : ?>
									<span class="screen-reader-text"><?php esc_html_e( '(nouvel onglet)', 'rolling' ); ?></span>
								<?php endif; ?>
							<?php echo $rolling_un_avis['lien'] ? '</a>' : '</span>'; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="rl-pied__bas">
			<span><?php echo esc_html( implode( ' · ', array_filter( array( Rolling_Entete_Pied::texte( 'rolling_pied_copyright' ), Rolling_Entete_Pied::texte( 'rolling_pied_mention' ) ) ) ) ); ?></span>
			<?php if ( has_nav_menu( 'pied' ) ) : ?>
				<nav aria-label="<?php echo esc_attr( (string) wp_get_nav_menu_name( 'pied' ) ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'pied',
							'container'      => false,
							'menu_class'     => 'rl-pied__legal',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				</nav>
			<?php endif; ?>
		</div>
	</div>
</footer>
<?php endif; ?>
