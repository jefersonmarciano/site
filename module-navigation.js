// Função para mostrar o conteúdo selecionado
function showContent(contentId) {
    const contents = document.querySelectorAll('.content');
    contents.forEach((content) => {
        content.classList.remove('active');
    });
    document.getElementById(contentId).classList.add('active');
}

// Função para encontrar o índice da div ativa
function getActiveIndex() {
    const contents = Array.from(document.querySelectorAll('.content'));
    return contents.findIndex(content => content.classList.contains('active'));
}

// Inicializar a navegação
function initNavigation() {
    // Evento para o botão "Next Step"
    document.getElementById('nextButton').addEventListener('click', () => {
        const contents = document.querySelectorAll('.content');
        let activeIndex = getActiveIndex();
        if (activeIndex < contents.length - 1) {
            showContent(contents[activeIndex + 1].id);
        }
    });

    // Evento para o botão "Back"
    document.getElementById('backButton').addEventListener('click', () => {
        const contents = document.querySelectorAll('.content');
        let activeIndex = getActiveIndex();
        if (activeIndex > 0) {
            showContent(contents[activeIndex - 1].id);
        }
    });

    // Verificar parâmetros de URL ao carregar a página
    const urlParams = new URLSearchParams(window.location.search);
    const lesson = urlParams.get('lesson');
    
    if (lesson === '2') {
        showContent('content2');
    } else if (lesson === '3') {
        showContent('content3');
    } else if (lesson === '4') {
        showContent('content4');
    }
    
    // Adicionar eventos de clique aos links do menu
    const menuLinks = document.querySelectorAll('.sidebar ul li a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const contentId = this.getAttribute('data-content');
            if (contentId) {
                showContent(contentId);
            }
        });
    });
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', initNavigation); 