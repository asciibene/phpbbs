# About the name
You shouldnt interpret the title of this website as "A wiki pertaining to php".
Its simply called like that because originally I was wanting to program a framework for making wikis in PHP...
However over time this idea got less and less appealing to me, I realised I wanted to make something more social than a wiki.
Maybe something in between a wiki and a bulletin board?
Maybe the proper name would have been along the lines of "PhpBBS" but a name is just a name, after all.


# Style and emphasis inside texts

You can use a form of markup to lightly style ypur texts.

## Headers
For now there is only a single level of heading.
To add one to the page you simply start a line with '##' followed by a mandatory space and then the heading's text.
The whitespace is important otherwise the line gets displayed as-is, and not as a header.
e.g. : ## This is a heading

## Emphasis
You can use style tags to put emphasis on words or group of words. 
It works on a per-line basis, meaning that each line of your text must contain both a opening and closing tag (Often they are the same token, but sometimes not, as with links).
If your line is missing a tag, the text is displayed as is. The same will happen if you try, for example, to underline a section that spans multiple line. it wont work.
It is meant to provide a simple way to underline single words although if you wish you could apply it to the whole line;
And then do the same for each line, this way you can reach the whole text.

Currently the following are available:
* Italics -> '//'
* Bold -> '**'
* Underline -> '__'

e.g. Consider this line, //this would be italics// and this normal.

## Links
You can link to other pages on the wiki.
However the link's text is always the title of page being linked to.
To insert a link anywhere inside a line, you must enclose the page's title between '-[' and ']-'.
The following example would link to a page named 'my page'.
e.g. : -[my page]-
If there is no page with that name, the line gets *displayed as-is.*