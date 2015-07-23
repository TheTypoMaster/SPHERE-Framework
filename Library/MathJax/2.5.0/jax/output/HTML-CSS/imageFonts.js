/*
 *  /MathJax/jax/output/HTML-CSS/imageFonts.js
 *
 *  Copyright (c) 2009-2015 The MathJax Consortium
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

(function( b, c, a )
{
    var d = "2.5.0";
    b.Register.LoadHook( c.fontDir + "/fontdata.js", function()
    {
        c.Augment( {
            allowWebFonts: false,
            imgDir: c.webfontDir + "/png",
            imgPacked: (MathJax.isPacked ? "" : "/unpacked"),
            imgSize: ["050", "060", "071", "085", 100, 120, 141, 168, 200, 238, 283, 336, 400, 476],
            imgBaseIndex: 4,
            imgSizeForEm: {},
            imgSizeForScale: {},
            imgZoom: 1,
            handleImg: function( t, i, r, h, u )
            {
                if (u.length) {
                    this.addText( t, u )
                }
                var s = r[5].orig;
                if (!s) {
                    s = r[5].orig = [r[0], r[1], r[2], r[3], r[4]]
                }
                var m = this.imgZoom;
                if (!t.scale) {
                    t.scale = 1
                }
                var p = this.imgIndex( t.scale * m );
                if (p == this.imgEmWidth.length - 1 && this.em * t.scale * m / this.imgEmWidth[p] > 1.1) {
                    m = this.imgEmWidth[p] / (this.em * t.scale)
                }
                var q = this.imgEmWidth[p] / (this.em * (t.scale || 1) * m);
                r[0] = s[0] * q;
                r[1] = s[1] * q;
                r[2] = s[2] * q;
                r[3] = s[3] * q;
                r[4] = s[4] * q;
                var k = this.imgDir + "/" + i.directory + "/" + this.imgSize[p];
                var l = h.toString( 16 ).toUpperCase();
                while (l.length < 4) {
                    l = "0" + l
                }
                var j = k + "/" + l + ".png";
                var o = r[5].img[p];
                var g = {width: Math.floor( o[0] / m + 0.5 ) + "px", height: Math.floor( o[1] / m + 0.5 ) + "px"};
                if (o[2]) {
                    g.verticalAlign = Math.floor( -o[2] / m + 0.5 ) + "px"
                }
                if (r[3] < 0) {
                    g.marginLeft = this.Em( r[3] / 1000 )
                }
                if (r[4] != r[2]) {
                    g.marginRight = this.Em( (r[2] - r[4]) / 1000 )
                }
                if (this.msieIE6) {
                    g.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + a.urlRev( j ) + "', sizingMethod='scale')";
                    j = this.directory + "/blank.gif"
                }
                this.addElement( t, "img", {src: a.urlRev( j ), style: g, isMathJax: true} );
                return ""
            },
            defineImageData: function( i )
            {
                for (var g in i) {
                    if (i.hasOwnProperty( g )) {
                        var h = c.FONTDATA.FONTS[g];
                        if (h) {
                            g = i[g];
                            for (var j in g) {
                                if (g.hasOwnProperty( j ) && h[j]) {
                                    h[j][5] = {img: g[j]}
                                }
                            }
                        }
                    }
                }
            },
            initImg: function( j )
            {
                if (this.imgSizeForEm[this.em]) {
                    this.imgBaseIndex = this.imgSizeForEm[this.em]
                }
                for (var h = 0, g = this.imgEmWidth.length - 1; h < g; h++) {
                    if (this.em <= this.imgEmWidth[h]) {
                        break
                    }
                }
                if (h && this.imgEmWidth[h] - this.em > this.em - this.imgEmWidth[h - 1]) {
                    h--
                }
                this.imgSizeForEm[this.em] = this.imgBaseIndex = h;
                this.imgZoom = this.imgBrowserZoom()
            },
            imgIndex: function( k )
            {
                if (!k) {
                    return this.imgBaseIndex
                }
                if (!this.imgSizeForScale[this.em]) {
                    this.imgSizeForScale[this.em] = {}
                }
                if (this.imgSizeForScale[this.em][k]) {
                    return this.imgSizeForScale[this.em][k]
                }
                var j = this.em * k;
                for (var h = 0, g = this.imgEmWidth.length - 1; h < g; h++) {
                    if (j <= this.imgEmWidth[h]) {
                        break
                    }
                }
                if (h && this.imgEmWidth[h] - j > j - this.imgEmWidth[h - 1]) {
                    h--
                }
                this.imgSizeForScale[this.em][k] = h;
                return h
            },
            imgBrowserZoom: function()
            {
                return 1
            }
        } );
        b.Browser.Select( {
            Firefox: function( h )
            {
                var g = c.addElement( document.body, "div", {
                    style: {
                        display: "none",
                        visibility: "hidden",
                        overflow: "scroll",
                        position: "absolute",
                        top: 0,
                        left: 0,
                        width: "200px",
                        height: "200px",
                        padding: 0,
                        border: 0,
                        margin: 0
                    }
                } );
                var i = c.addElement( g, "div", {
                    style: {
                        position: "absolute",
                        left: 0,
                        top: 0,
                        right: 0,
                        bottom: 0,
                        padding: 0,
                        border: 0,
                        margin: 0
                    }
                } );
                c.Augment( {
                    imgSpaceBug: true,
                    imgSpace: "\u00A0",
                    imgZoomLevel: (h.isMac ? {
                        50: 0.3,
                        30: 0.5,
                        22: 0.67,
                        19: 0.8,
                        16: 0.9,
                        15: 1,
                        13: 1.1,
                        12: 1.2,
                        11: 1.33,
                        10: 1.5,
                        9: 1.7,
                        7: 2,
                        6: 2.4,
                        5: 3,
                        0: 15
                    } : {
                        56: 0.3,
                        34: 0.5,
                        25: 0.67,
                        21: 0.8,
                        19: 0.9,
                        17: 1,
                        15: 1.1,
                        14: 1.2,
                        13: 1.33,
                        11: 1.5,
                        10: 1.7,
                        8: 2,
                        7: 2.4,
                        6: 3,
                        0: 17
                    }),
                    imgZoomDiv: g,
                    imgBrowserZoom: function()
                    {
                        var j = this.imgZoomLevel;
                        g.style.display = "";
                        var k = (g.offsetWidth - i.offsetWidth);
                        k = (j[k] ? j[k] : j[0] / k);
                        g.style.display = "none";
                        return k
                    }
                } )
            }, Safari: function( g )
            {
                c.Augment( {
                    imgBrowserZoom: function()
                    {
                        return 3
                    }
                } )
            }, Chrome: function( g )
            {
                c.Augment( {
                    imgHeightBug: true, imgBrowserZoom: function()
                    {
                        return 3
                    }
                } )
            }, Opera: function( g )
            {
                c.Augment( {
                    imgSpaceBug: true,
                    imgSpace: "\u00A0\u00A0",
                    imgDoc: (document.compatMode == "BackCompat" ? document.body : document.documentElement),
                    imgBrowserZoom: function()
                    {
                        if (g.isMac) {
                            return 3
                        }
                        var h = this.imgDoc.clientHeight, i = Math.floor( 15 * h / window.innerHeight );
                        if (this.imgDoc.clientWidth < this.imgDoc.scrollWidth - i) {
                            h += i
                        }
                        return parseFloat( (window.innerHeight / h).toFixed( 1 ) )
                    }
                } )
            }
        } );
        var f = function()
        {
            var h = c.FONTDATA.FONTS.MathJax_Main[8212][5].img;
            c.imgEmWidth = [];
            for (var j = 0, g = h.length; j < g; j++) {
                c.imgEmWidth[j] = h[j][0]
            }
        };
        var e = c.imgDir + c.imgPacked;
        MathJax.Callback.Queue( ["Require", a, e + "/imagedata.js"], f,
            ["loadComplete", a, c.directory + "/imageFonts.js"] )
    } )
})( MathJax.Hub, MathJax.OutputJax["HTML-CSS"], MathJax.Ajax );
