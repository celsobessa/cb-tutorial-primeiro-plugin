<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.celsobessa.com.br/plugins/cb-tutorial-primeiro-plugin
 * @since             0.1.0
 *
 * @wordpress-plugin
 * Plugin Name:       Celso Bessa - Tutorial Primeiro Plugin
 * Plugin URI:        https://www.celsobessa.com.br/plugins/cb-tutorial-primeiro-plugin
 * Description:       A plugin created for a "First Plugin" tutorial
 * Version:           0.1.0
 * Author:            Celso Bessa
 * Author URI:        https://www.celsobessa.com.br/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cb-sandbox-control-room
 * Domain Path:       /languages
 *
 * @package CB_Tutorial_Primeiro_Plugin
 */

// If this file is called directly, abort.
// se o arquivo for chamado diretamente, aborte.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// useful constants: plugin's version and path.
// constantes úteis: versão e caminho do plugin.
define( 'CBTPL_PLUGIN_VERSION', '0.1.0' );
define( 'CBTPL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

if ( is_admin() ) {

	/**
	 * Bloqueia acesso ao painel condicionalmente
	 *
	 * Bloqueia acesso ao painel se não for administrador ou editor
	 *
	 * @since  0.1.0
	 * @return void Markup HTML para o aviso.
	 */
	function cbtpl_blockusers_init() {

		// verifica se o usuário pode usar manage_options.
		// veja Roles e Capabilities em https://codex.wordpress.org/Roles_and_Capabilities .
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			wp_safe_redirect( home_url() );
			exit;
		}
	}
	add_action( 'init', 'cbtpl_blockusers_init' );

	/**
	 * Gerencia os avisos para o plugin.
	 *
	 * Gerencia os avisos para o plugin.
	 *
	 * @since  0.1.0
	 * @return void Markup HTML para o aviso.
	 */
	function cbtpl_notices() {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php echo 'A vida é muito curta para não comer pão de queijo do Na Venda Pão de Queijaria'; ?></p>
		</div>
	<?php
	}
	add_action( 'admin_notices', 'cbtpl_notices' );

}

if ( ! is_admin() ) {

	// Remove a barra de admin na área pública.
	add_filter( 'show_admin_bar', '__return_false' );

	// remove diversos ítens do wp_head.
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'start_post_rel_link' );
	remove_action( 'wp_head', 'index_rel_link' );

	// note o uso do terceiro parâmetro, prioridade.
	// via de grega, remoções de hooks devem usar a mesma prioridade.
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'feed_links', 2 );

	// note o uso do quarto parâmetro, prioridade, número de parâmetros da função hook.
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

	/**
	 * Filtra as classes do elemento body
	 *
	 * Filtra condicionalmente as as classes do elemento body conforme a seção do site
	 *
	 * @since  0.1.0
	 * @param array $classes As classes do elemento body.
	 * @return array $classes As classes do elemento body, filtradas.
	 */
	function cbtpl_body_class( $classes ) {

		// Adiciona uma classe geral para o plugin.
		$classes[] = 'cbtpl';

		// Adiciona uma classe se for page, se for post, se for archive ou se for home.
		if ( is_page() ) {
			$classes[] = 'cbtpl-page';
		} else if ( is_single() ) {
			$classes[] = 'cbtpl-single';
		} else if ( is_archive() ) {
			$classes[] = 'cbtpl-archive';
		} else if ( is_home() ) {
			$classes[] = 'cbtpl-home';
		}

		// Retorna as classes filtradas.
		return $classes;

	}
	add_filter( 'body_class','cbtpl_body_class' );


	/**
	 * Adiciona um iframe com o mapa japu ao final do conteúdo.
	 *
	 * Adiciona um iframe com o mapa japu ao final do conteúdo de posts and pages. Adaptado do
	 * plugin oficial do Japu (veja https://github.com/2aces/japu-map-embedder/).
	 *
	 * @since 0.1.0
	 * @param string $content O conteúdo do post.
	 * @return string $content O conteúdo do post, filtrado, com o iframe ao final.
	 */
	function cbtpl_add_japu_map( $content ) {

		// se não estivermos em post ou page, retorna $content sem modificar e sai da função.
		if ( ! is_single() && ! is_page() ) {
			return $content;
		}
		global $post;

		// cria uma string $markup para o html gerado.
		$markup = '<div class="japumap-wrapper">';
		$markup .= '<iframe src="https://embed.japuapp.com.br/" sandbox="allow-same-origin allow-scripts allow-forms" style="border:none;" class="japumap-iframe"></iframe>';
		$markup .= '<div class="japumap-bottom-bar">';
		$markup .= '<p class="japumap-credits">';
		$markup .= 'fonte: <a href="https://www.japuapp.com.br">Japu - Rotas das Vertentes</a>';
		$markup .= '<a id="japumap-toggle-full-screen"><span class="return-text">sair da tela cheia e voltar ao site</span><span class="toggle-full-screen-text">tela cheia</span></a>';
		$markup .= '</p>';
		$markup .= '</div>';
		$markup .= '</div>';

		// adiciona $markup após $content.
		$content .= $markup;

		// retorn $content.
		return $content;
	}

	// prioridade alta para ser usada após outros plugins também filtrarem o conteúdo.
	add_filter( 'the_content', 'cbtpl_add_japu_map', 100 );

	/**
	 * Adiciona os estilos e scripts customizados.
	 *
	 * Adiciona os estilos e scripts customizados de nosso plugin.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	function cbtpl_ativos_publicos() {

		// Registra e coloca na fila nosso estilo customizado.
		wp_register_style( 'cbtpl-style', plugin_dir_url( __FILE__ ) . 'includes/styles/cbtpl-style.css' , array(), CBTPL_PLUGIN_VERSION, 'all' );
		wp_enqueue_style( 'cbtpl-style' );

		// Registra e coloca na fila nosso script customizado.
		// TODO: condicional para single e page.
		wp_register_script( 'cbtpl-script', plugin_dir_url( __FILE__ ) . 'includes/js/cbtpl-script.js' , array(), CBTPL_PLUGIN_VERSION, true );
		wp_enqueue_script( 'cbtpl-script' );
	}
	add_action( 'wp_enqueue_scripts', 'cbtpl_ativos_publicos' );

}
