/*
 *  /MathJax/jax/output/SVG/autoload/mtable.js
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

MathJax.Hub.Register.StartupHook( "SVG Jax Ready", function()
{
    var c = "2.5.0";
    var a = MathJax.ElementJax.mml, d = MathJax.OutputJax.SVG, b = d.BBOX;
    a.mtable.Augment( {
        toSVG: function( X )
        {
            this.SVGgetStyles();
            var o = this.SVG(), am = this.SVGgetScale( o );
            if (this.data.length === 0) {
                this.SVGsaveData( o );
                return o
            }
            var aJ = this.getValues( "columnalign", "rowalign", "columnspacing", "rowspacing", "columnwidth",
                "equalcolumns", "equalrows", "columnlines", "rowlines", "frame", "framespacing", "align", "useHeight",
                "width", "side", "minlabelspacing" );
            if (aJ.width.match( /%$/ )) {
                o.width = aJ.width = d.Em( (d.cwidth / 1000) * (parseFloat( aJ.width ) / 100) )
            }
            var u = this.SVGgetMu( o );
            var aG = -1;
            var w = [], G = [], k = [], N = [], I = [], aE, aD, v = -1, aB, t, aw, Q, ak, B, ax;
            var aa = d.FONTDATA.lineH * am * aJ.useHeight, ae = d.FONTDATA.lineD * am * aJ.useHeight;
            for (aE = 0, aB = this.data.length; aE < aB; aE++) {
                Q = this.data[aE];
                aw = (Q.type === "mlabeledtr" ? aG : 0);
                N[aE] = [];
                w[aE] = aa;
                G[aE] = ae;
                for (aD = aw, t = Q.data.length + aw; aD < t; aD++) {
                    if (k[aD] == null) {
                        if (aD > v) {
                            v = aD
                        }
                        I[aD] = b.G();
                        k[aD] = -d.BIGDIMEN
                    }
                    ak = Q.data[aD - aw];
                    N[aE][aD] = ak.toSVG();
                    if (ak.isEmbellished()) {
                        B = ak.CoreMO();
                        var aI = B.Get( "minsize", true );
                        if (aI) {
                            if (B.SVGcanStretch( "Vertical" )) {
                                ax = B.SVGdata.h + B.SVGdata.d;
                                if (ax) {
                                    aI = d.length2em( aI, u, ax );
                                    if (aI * B.SVGdata.h / ax > w[aE]) {
                                        w[aE] = aI * B.SVGdata.h / ax
                                    }
                                    if (aI * B.SVGdata.d / ax > G[aE]) {
                                        G[aE] = aI * B.SVGdata.d / ax
                                    }
                                }
                            } else {
                                if (B.SVGcanStretch( "Horizontal" )) {
                                    aI = d.length2em( aI, u, B.SVGdata.w );
                                    if (aI > k[aD]) {
                                        k[aD] = aI
                                    }
                                }
                            }
                        }
                    }
                    if (N[aE][aD].h > w[aE]) {
                        w[aE] = N[aE][aD].h
                    }
                    if (N[aE][aD].d > G[aE]) {
                        G[aE] = N[aE][aD].d
                    }
                    if (N[aE][aD].w > k[aD]) {
                        k[aD] = N[aE][aD].w
                    }
                }
            }
            var al = MathJax.Hub.SplitList;
            var aj = al( aJ.columnspacing ), R = al( aJ.rowspacing ), ag = al( aJ.columnalign ), K = al( aJ.rowalign ), L = al( aJ.columnlines ), h = al( aJ.rowlines ), ao = al( aJ.columnwidth ), at = [];
            for (aE = 0, aB = aj.length; aE < aB; aE++) {
                aj[aE] = d.length2em( aj[aE], u )
            }
            for (aE = 0, aB = R.length; aE < aB; aE++) {
                R[aE] = d.length2em( R[aE], u )
            }
            while (aj.length < v) {
                aj.push( aj[aj.length - 1] )
            }
            while (ag.length <= v) {
                ag.push( ag[ag.length - 1] )
            }
            while (L.length < v) {
                L.push( L[L.length - 1] )
            }
            while (ao.length <= v) {
                ao.push( ao[ao.length - 1] )
            }
            while (R.length < N.length) {
                R.push( R[R.length - 1] )
            }
            while (K.length <= N.length) {
                K.push( K[K.length - 1] )
            }
            while (h.length < N.length) {
                h.push( h[h.length - 1] )
            }
            if (I[aG]) {
                ag[aG] = (aJ.side.substr( 0, 1 ) === "l" ? "left" : "right");
                aj[aG] = -k[aG]
            }
            for (aE = 0, aB = N.length; aE < aB; aE++) {
                Q = this.data[aE];
                at[aE] = [];
                if (Q.rowalign) {
                    K[aE] = Q.rowalign
                }
                if (Q.columnalign) {
                    at[aE] = al( Q.columnalign );
                    while (at[aE].length <= v) {
                        at[aE].push( at[aE][at[aE].length - 1] )
                    }
                }
            }
            if (aJ.equalrows) {
                var S = Math.max.apply( Math, w ), an = Math.max.apply( Math, G );
                for (aE = 0, aB = N.length; aE < aB; aE++) {
                    aw = ((S + an) - (w[aE] + G[aE])) / 2;
                    w[aE] += aw;
                    G[aE] += aw
                }
            }
            ax = w[0] + G[N.length - 1];
            for (aE = 0, aB = N.length - 1; aE < aB; aE++) {
                ax += Math.max( 0, G[aE] + w[aE + 1] + R[aE] )
            }
            var ac = 0, Z = 0, ay, aH = ax;
            if (aJ.frame !== "none" || (aJ.columnlines + aJ.rowlines).match( /solid|dashed/ )) {
                var r = al( aJ.framespacing );
                if (r.length != 2) {
                    r = al( this.defaults.framespacing )
                }
                ac = d.length2em( r[0], u );
                Z = d.length2em( r[1], u );
                aH = ax + 2 * Z
            }
            var g, av, aA = "";
            if (typeof(aJ.align) !== "string") {
                aJ.align = String( aJ.align )
            }
            if (aJ.align.match( /(top|bottom|center|baseline|axis)( +(-?\d+))?/ )) {
                aA = RegExp.$3 || "";
                aJ.align = RegExp.$1
            } else {
                aJ.align = this.defaults.align
            }
            if (aA !== "") {
                aA = parseInt( aA );
                if (aA < 0) {
                    aA = N.length + 1 + aA
                }
                if (aA < 1) {
                    aA = 1
                } else {
                    if (aA > N.length) {
                        aA = N.length
                    }
                }
                g = 0;
                av = -(ax + Z) + w[0];
                for (aE = 0, aB = aA - 1; aE < aB; aE++) {
                    var V = Math.max( 0, G[aE] + w[aE + 1] + R[aE] );
                    g += V;
                    av += V
                }
            } else {
                g = ({
                    top: -(w[0] + Z),
                    bottom: ax + Z - w[0],
                    center: ax / 2 - w[0],
                    baseline: ax / 2 - w[0],
                    axis: ax / 2 + d.TeX.axis_height * am - w[0]
                })[aJ.align];
                av = ({
                    top: -(ax + 2 * Z),
                    bottom: 0,
                    center: -(ax / 2 + Z),
                    baseline: -(ax / 2 + Z),
                    axis: d.TeX.axis_height * am - ax / 2 - Z
                })[aJ.align]
            }
            var ap, au = 0, T = 0, U = 0, az = 0, aF = 0, q = [], z = [], ah = 1;
            if (aJ.equalcolumns && aJ.width !== "auto") {
                ap = d.length2em( aJ.width, u );
                for (aE = 0, aB = Math.min( v + 1, aj.length ); aE < aB; aE++) {
                    ap -= aj[aE]
                }
                ap /= v + 1;
                for (aE = 0, aB = Math.min( v + 1, ao.length ); aE < aB; aE++) {
                    k[aE] = ap
                }
            } else {
                for (aE = 0, aB = Math.min( v + 1, ao.length ); aE < aB; aE++) {
                    if (ao[aE] === "auto") {
                        T += k[aE]
                    } else {
                        if (ao[aE] === "fit") {
                            z[aF] = aE;
                            aF++;
                            T += k[aE]
                        } else {
                            if (ao[aE].match( /%$/ )) {
                                q[az] = aE;
                                az++;
                                U += k[aE];
                                au += d.length2em( ao[aE], u, 1 )
                            } else {
                                k[aE] = d.length2em( ao[aE], u );
                                T += k[aE]
                            }
                        }
                    }
                }
                if (aJ.width === "auto") {
                    if (au > 0.98) {
                        ah = U / (T + U);
                        ap = T + U
                    } else {
                        ap = T / (1 - au)
                    }
                } else {
                    ap = d.length2em( aJ.width, u );
                    for (aE = 0, aB = Math.min( v + 1, aj.length ); aE < aB; aE++) {
                        ap -= aj[aE]
                    }
                }
                for (aE = 0, aB = q.length; aE < aB; aE++) {
                    k[q[aE]] = d.length2em( ao[q[aE]], u, ap * ah );
                    T += k[q[aE]]
                }
                if (Math.abs( ap - T ) > 0.01) {
                    if (aF && ap > T) {
                        ap = (ap - T) / aF;
                        for (aE = 0, aB = z.length; aE < aB; aE++) {
                            k[z[aE]] += ap
                        }
                    } else {
                        ap = ap / T;
                        for (aD = 0; aD <= v; aD++) {
                            k[aD] *= ap
                        }
                    }
                }
                if (aJ.equalcolumns) {
                    var ad = Math.max.apply( Math, k );
                    for (aD = 0; aD <= v; aD++) {
                        k[aD] = ad
                    }
                }
            }
            var aq = g, l, aC;
            aw = (I[aG] ? aG : 0);
            for (aD = aw; aD <= v; aD++) {
                I[aD].w = k[aD];
                for (aE = 0, aB = N.length; aE < aB; aE++) {
                    if (N[aE][aD]) {
                        aw = (this.data[aE].type === "mlabeledtr" ? aG : 0);
                        ak = this.data[aE].data[aD - aw];
                        if (ak.SVGcanStretch( "Horizontal" )) {
                            N[aE][aD] = ak.SVGstretchH( k[aD] )
                        } else {
                            if (ak.SVGcanStretch( "Vertical" )) {
                                B = ak.CoreMO();
                                var ab = B.symmetric;
                                B.symmetric = false;
                                N[aE][aD] = ak.SVGstretchV( w[aE], G[aE] );
                                B.symmetric = ab
                            }
                        }
                        aC = ak.rowalign || this.data[aE].rowalign || K[aE];
                        l = ({
                            top: w[aE] - N[aE][aD].h,
                            bottom: N[aE][aD].d - G[aE],
                            center: ((w[aE] - G[aE]) - (N[aE][aD].h - N[aE][aD].d)) / 2,
                            baseline: 0,
                            axis: 0
                        })[aC] || 0;
                        aC = (ak.columnalign || at[aE][aD] || ag[aD]);
                        I[aD].Align( N[aE][aD], aC, 0, aq + l )
                    }
                    if (aE < N.length - 1) {
                        aq -= Math.max( 0, G[aE] + w[aE + 1] + R[aE] )
                    }
                }
                aq = g
            }
            var af = 1.5 * d.em;
            var ar = ac - af / 2;
            for (aD = 0; aD <= v; aD++) {
                o.Add( I[aD], ar, 0 );
                ar += k[aD] + aj[aD];
                if (L[aD] !== "none" && aD < v && aD !== aG) {
                    o.Add( b.VLINE( aH, af, L[aD] ), ar - aj[aD] / 2, av )
                }
            }
            o.w += ac;
            o.d = -av;
            o.h = aH + av;
            ay = o.w;
            if (aJ.frame !== "none") {
                o.Add( b.HLINE( ay, af, aJ.frame ), 0, av + aH - af );
                o.Add( b.HLINE( ay, af, aJ.frame ), 0, av );
                o.Add( b.VLINE( aH, af, aJ.frame ), 0, av );
                o.Add( b.VLINE( aH, af, aJ.frame ), ay - af, av )
            }
            aq = g - af / 2;
            for (aE = 0, aB = N.length - 1; aE < aB; aE++) {
                l = Math.max( 0, G[aE] + w[aE + 1] + R[aE] );
                if (h[aE] !== "none") {
                    o.Add( b.HLINE( ay, af, h[aE] ), 0, aq - G[aE] - (l - G[aE] - w[aE + 1]) / 2 )
                }
                aq -= l
            }
            o.Clean();
            this.SVGhandleSpace( o );
            this.SVGhandleColor( o );
            if (I[aG]) {
                o.tw = Math.max( o.w, o.r ) - Math.min( 0, o.l );
                var O = this.getValues( "indentalignfirst", "indentshiftfirst", "indentalign", "indentshift" );
                if (O.indentalignfirst !== a.INDENTALIGN.INDENTALIGN) {
                    O.indentalign = O.indentalignfirst
                }
                if (O.indentalign === a.INDENTALIGN.AUTO) {
                    O.indentalign = this.displayAlign
                }
                if (O.indentshiftfirst !== a.INDENTSHIFT.INDENTSHIFT) {
                    O.indentshift = O.indentshiftfirst
                }
                if (O.indentshift === "auto" || O.indentshift === "") {
                    O.indentshift = "0"
                }
                var ai = d.length2em( O.indentshift, u, d.cwidth );
                var aK = d.length2em( aJ.minlabelspacing, u, d.cwidth );
                if (this.displayIndent !== "0") {
                    var e = d.length2em( this.displayIndent, u, d.cwidth );
                    ai += (O.indentAlign === a.INDENTALIGN.RIGHT ? -e : e)
                }
                var E = o;
                o = this.SVG();
                o.w = o.r = d.cwidth;
                o.hasIndent = true;
                o.Align( I[aG], ag[aG], aK, 0 );
                o.Align( E, O.indentalign, 0, 0, ai );
                o.tw += I[aG].w + ai + (O.indentalign === a.INDENTALIGN.CENTER ? 8 : 4) * aK
            }
            this.SVGsaveData( o );
            return o
        }, SVGhandleSpace: function( e )
        {
            if (!this.hasFrame && !e.width) {
                e.x = e.X = 167
            }
            this.SUPER( arguments ).SVGhandleSpace.call( this, e )
        }
    } );
    a.mtd.Augment( {
        toSVG: function( e, g )
        {
            var f = this.svg = this.SVG();
            if (this.data[0]) {
                f.Add( this.SVGdataStretched( 0, e, g ) );
                f.Clean()
            }
            this.SVGhandleColor( f );
            this.SVGsaveData( f );
            return f
        }
    } );
    MathJax.Hub.Startup.signal.Post( "SVG mtable Ready" );
    MathJax.Ajax.loadComplete( d.autoloadDir + "/mtable.js" )
} );
