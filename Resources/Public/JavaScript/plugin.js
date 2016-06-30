var SnippetPreview = require( "yoastseo" ).SnippetPreview;
var App = require( "yoastseo" ).App;

var focusKeywordField = document.getElementById( "focusKeyword" );
// var contentField = document.getElementById( "content" );

var snippetPreview = new SnippetPreview({
    targetElement: document.getElementById( "snippet" )
});

var app = new App({
    snippetPreview: snippetPreview,
    targets: {
        output: 'seo',
        contentOutput: 'readability'
    },
    callbacks: {
        getData: function() {
            return {
                keyword: document.querySelector('[data-yoast-focuskeyword]').getAttribute('data-yoast-focuskeyword'),
                text: 'bla'
            };
        }
    }
});

app.refresh();

var toggles = document.querySelectorAll('[data-controls]');
for (var i = 0; i < toggles.length; i++) {
    toggles[i].addEventListener('click', function () {
        this.querySelector('.caret').classList.toggle('caret--closed');
        document.getElementById(this.getAttribute('data-controls')).classList.toggle('yoastPanel__content--open');
    });
}

// focusKeywordField.addEventListener( 'change', app.refresh.bind( app ) );
// contentField.addEventListener( 'change', app.refresh.bind( app ) );