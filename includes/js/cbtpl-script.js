'use strict'
/**
 * Inicializa o Mapa Japu.
 *
 * Inicializa o Mapa Japu, ouvindo cliques para deixá-lo em tela cheia.
 *
 * @since  0.1.0
 * @return void
 */
function japuMapEmbedInit() {

	// procura o elemento que envolve o mapa.
	const japuMapWrapperElement = document.querySelector(".japumap-wrapper");

	// procura o selector com o botão full screen.
	const japuFullScreenButtonElement = document.querySelector("#japumap-toggle-full-screen");

	// adiciona um EventListener para clique no seletor do botão full screen.
	japuFullScreenButtonElement.addEventListener('click', function () {

		// alterna a classe a full screen no elemento que envolve o mapa.
		japuMapWrapperElement.classList.toggle('fullscreen');

		// alterna a classe a full screen no elemento do botão full screen.
		japuFullScreenButtonElement.classList.toggle('fullscreen');
	});
}

// testa suporte à addEventLister e aciona a função japuMapEmbedInit no evento load da janela.
if (window.addEventListener) {
	window.addEventListener('load', japuMapEmbedInit, false);
}
else if (window.attachEvent) {
	window.attachEvent("onload", japuMapEmbedInit);
}
else {
	window.onload = japuMapEmbedInit;
};