# PHP BBS
## TODO 
- [ ] finish rewrite of proc to class
- [ ] fix DB unserialize stuff
- [ ] clean the pseudo-html files (e.g. .php files served inside root folder)
- [ ] Less boilerplate code inside said files
- [ ] Gemini protocol gateway proxy (Work has not even begun)


## About markup format available inside posts:

You can use a form of markup to lightly style your posts.

### Headers
For now there is only a single level of heading.
To add one to the page you simply start a line with '##' followed by a mandatory space and then the heading's text.
The whitespace is important otherwise the line gets displayed as-is, and not as a header.
e.g. : ## This is a heading

### Emphasis
You can use style tags to put emphasis on words or group of words. 
It works on a per-line basis, meaning that each line of your text must contain both a opening and closing tag (Often they are the same token, but sometimes not, as with links).
If your line is missing a tag, the text is displayed as is. The same will happen if you try, for example, to underline a section that spans multiple line. it wont work.
It is meant to provide a simple way to underline single words although if you wish you could apply it to the whole line;
And then do the same for each line, this way you can reach the whole text.

Currently the following are available:
* Italics -> '//'
* Bold -> '**'
* Underline -> '__'

e.g. Consider this line, //this would be italics// and this would be normal.

### making links 
You can link to other posts on the bbs.
__However the link's text is always the title of page being linked to.__
To insert a link anywhere inside a line, you must enclose the page's title between '-[' and ']-'.
The following example would link to a page named 'my page'.
e.g. : -[my page]-
If there is no page with that name, the line gets *displayed as-is.*
