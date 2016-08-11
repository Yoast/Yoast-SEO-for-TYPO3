module.exports = {
    all: {
        options: {
            format: "jed1.x",
            domain: "js-text-analysis",
        },
        src: [ "<%= paths.languages %>*.po" ],
        dest: "<%= paths.languages %>",
    }
};