angular.module('libroApp.security', [])
    .service('credenciales', ['xwsse',
        function(xwsse) {
            var username = '';
            var password = '';
            return {
                getUser: function() {
                    return this.username;
                },
                setUser: function(user) {
                    this.username = user;
                },
                getPass: function() {
                    return this.password;
                },
                setPass: function(pass) {
                    var shaObj = new jsSHA(pass, "TEXT");
                    this.password = shaObj.getHash("SHA-512", "B64");
                },
                isLogged: function() {
                    if (!this.username || !this.password) {
                        return false;
                    }
                    return true;
                },
                logOut: function() {
                    this.username = '';
                    this.password = '';
                },
                getXWSSE: function() {
                    return xwsse.calc(this.username, this.password);
                }
            };
        }
    ])
    .service('base64', function() {
        this.base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
        this.base64DecodeChars = new Array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1);
        this.base64encode = function(str) {
            var out, i, len;
            var c1, c2, c3;
            len = str.length;
            i = 0;
            out = "";
            while (i < len) {
                c1 = str.charCodeAt(i++) & 0xff;
                if (i == len) {
                    out += this.base64EncodeChars.charAt(c1 >> 2);
                    out += this.base64EncodeChars.charAt((c1 & 0x3) << 4);
                    out += "==";
                    break;
                }
                c2 = str.charCodeAt(i++);
                if (i == len) {
                    out += this.base64EncodeChars.charAt(c1 >> 2);
                    out += this.base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
                    out += this.base64EncodeChars.charAt((c2 & 0xF) << 2);
                    out += "=";
                    break;
                }
                c3 = str.charCodeAt(i++);
                out += this.base64EncodeChars.charAt(c1 >> 2);
                out += this.base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
                out += this.base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
                out += this.base64EncodeChars.charAt(c3 & 0x3F);
            }
            return out;
        }
        this.base64decode = function(str) {
            var c1, c2, c3, c4;
            var i, len, out;
            len = str.length;
            i = 0;
            out = "";
            while (i < len) {
                /* c1 */
                do {
                    c1 = this.base64DecodeChars[str.charCodeAt(i++) & 0xff];
                } while (i < len && c1 == -1);
                if (c1 == -1) break;
                /* c2 */
                do {
                    c2 = this.base64DecodeChars[str.charCodeAt(i++) & 0xff];
                } while (i < len && c2 == -1);
                if (c2 == -1) break;
                out += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));
                /* c3 */
                do {
                    c3 = str.charCodeAt(i++) & 0xff;
                    if (c3 == 61) return out;
                    c3 = this.base64DecodeChars[c3];
                } while (i < len && c3 == -1);
                if (c3 == -1) break;
                out += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2));
                /* c4 */
                do {
                    c4 = str.charCodeAt(i++) & 0xff;
                    if (c4 == 61) return out;
                    c4 = this.base64DecodeChars[c4];
                } while (i < len && c4 == -1);
                if (c4 == -1) break;
                out += String.fromCharCode(((c3 & 0x03) << 6) | c4);
            }
            return out;
        }
    })
    .service('xwsse', ['base64',
        function(base64) {
            this.b64pad = "=";
            this.calc = function(ousername, opassword) {
                var userName = ousername;
                var password = opassword;
                var nonce = this.generateNonce(16);
                var nonce64 = base64.base64encode(nonce);
                var created = this.getW3CDate(new Date());
                var unHashedPasswordDigest = nonce + created + password;
                var shaObj = new jsSHA(unHashedPasswordDigest, "TEXT");
                var passwordDigest = shaObj.getHash("SHA-512", "B64");
                var digest = "UsernameToken Username=\"" + userName + "\", PasswordDigest=\"" + passwordDigest + "\", Nonce=\"" + nonce64 + "\", Created=\"" + created + "\"";
                return digest;
            };
            this.generateNonce = function(length) {
                var nonceChars = "0123456789abcdef";
                var result = "";
                for (var i = 0; i < length; i++) {
                    result += nonceChars.charAt(Math.floor(Math.random() * nonceChars.length));
                }
                return result;
            };
            this.getW3CDate = function(date) {
                var yyyy = date.getUTCFullYear();
                var mm = (date.getUTCMonth() + 1);
                if (mm < 10) {
                    mm = "0" + mm;
                }
                var dd = (date.getUTCDate());
                if (dd < 10) {
                    dd = "0" + dd;
                }
                var hh = (date.getUTCHours());
                if (hh < 10) {
                    hh = "0" + hh;
                }
                var mn = (date.getUTCMinutes());
                if (mn < 10) {
                    mn = "0" + mn;
                }
                var ss = (date.getUTCSeconds());
                if (ss < 10) {
                    ss = "0" + ss;
                }
                return yyyy + "-" + mm + "-" + dd + "T" + hh + ":" + mn + ":" + ss + "Z";
            };
        }
    ])
    .factory('Socio', ['$resource',
        function($resource) {
            return $resource('/apisocios', {}, {
                query: {
                    method: 'GET',
                    interceptor: {
                        response: function(data) {
                            console.log('response in interceptor', data);
                        },
                        responseError: function(data) {
                            console.log('error in interceptor', data);
                        }
                    },
                    isArray: true
                }
            });
        }
    ]);
