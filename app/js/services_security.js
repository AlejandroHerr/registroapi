libroServices.service('galletitas', ['$cookies', 'xwsse',
	function ($cookies, xwsse) {
		this.isLogged = function () {
			if(!$cookies.username || !$cookies.password) {
				return false;
			}
			return true;
		}
		this.get = function (str) {
			return $cookies[str];
		}
		this.getXWSSE = function () {
			return xwsse.calc($cookies.username, $cookies.password);
		}
}]);
libroServices.service('credenciales', ['xwsse',
	function (xwsse) {
		var username = '';
		var password = '';
		return{
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
				this.password = pass;
			},
			isLogged: function(){
				if(!this.username || !this.password) {
					return false;
				}
				return true;
			},
			logOut: function(){
				this.username='';
				this.password='';
			}
		};
}]);
libroServices.service('base64', function () {
	this.base64EncodeChars =
		"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
	this.base64DecodeChars = new Array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -
		1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -
		1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63,
		52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2,
		3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
		15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, -1, 26, 27,
		28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
		41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1);
	this.base64encode = function (str) {
		var out, i, len;
		var c1, c2, c3;
		len = str.length;
		i = 0;
		out = "";
		while(i < len) {
			c1 = str.charCodeAt(i++) & 0xff;
			if(i == len) {
				out += this.base64EncodeChars.charAt(c1 >> 2);
				out += this.base64EncodeChars.charAt((c1 & 0x3) << 4);
				out += "==";
				break;
			}
			c2 = str.charCodeAt(i++);
			if(i == len) {
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
	this.base64decode = function (str) {
		var c1, c2, c3, c4;
		var i, len, out;
		len = str.length;
		i = 0;
		out = "";
		while(i < len) {
			/* c1 */
			do {
				c1 = this.base64DecodeChars[str.charCodeAt(i++) & 0xff];
			} while (i < len && c1 == -1);
			if(c1 == -1)
				break;
			/* c2 */
			do {
				c2 = this.base64DecodeChars[str.charCodeAt(i++) & 0xff];
			} while (i < len && c2 == -1);
			if(c2 == -1)
				break;
			out += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));
			/* c3 */
			do {
				c3 = str.charCodeAt(i++) & 0xff;
				if(c3 == 61)
					return out;
				c3 = this.base64DecodeChars[c3];
			} while (i < len && c3 == -1);
			if(c3 == -1)
				break;
			out += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2));
			/* c4 */
			do {
				c4 = str.charCodeAt(i++) & 0xff;
				if(c4 == 61)
					return out;
				c4 = this.base64DecodeChars[c4];
			} while (i < len && c4 == -1);
			if(c4 == -1)
				break;
			out += String.fromCharCode(((c3 & 0x03) << 6) | c4);
		}
		return out;
	}
});
libroServices.service('sha1', function () {
	this.hexcase = 0; /* hex output format. 0 - lowercase; 1 - uppercase        */
	this.b64pad = "="; /* base-64 pad character. "=" for strict RFC compliance   */
	this.chrsz = 8; /* bits per input character. 8 - ASCII; 16 - Unicode      */
	/*
	 * These are the functions you'll usually want to call
	 * They take string arguments and return either hex or base-64 encoded strings
	 */
	this.hex_sha1 = function (s) {
		return this.binb2hex(this.core_sha1(this.str2binb(s), s.length * this.chrsz));
	}
	this.b64_sha1 = function (s) {
		return this.binb2b64(this.core_sha1(this.str2binb(s), s.length * this.chrsz));
	}
	this.str_sha1 = function (s) {
		return this.binb2str(this.core_sha1(this.str2binb(s), s.length * this.chrsz));
	}
	this.hex_hmac_sha1 = function (key, data) {
		return this.binb2hex(this.core_hmac_sha1(key, data));
	}
	this.b64_hmac_sha1 = function (key, data) {
		return this.binb2b64(this.core_hmac_sha1(key, data));
	}
	this.str_hmac_sha1 = function (key, data) {
		return this.binb2str(this.core_hmac_sha1(key, data));
	}
	/*
	 * Perform a simple self-test to see if the VM is working
	 */
	this.sha1_vm_test = function () {
		return this.hex_sha1("abc") == "a9993e364706816aba3e25717850c26c9cd0d89d";
	}
	/*
	 * Calculate the SHA-1 of an array of big-endian words, and a bit length
	 */
	this.core_sha1 = function (x, len) {
		/* append padding */
		x[len >> 5] |= 0x80 << (24 - len % 32);
		x[((len + 64 >> 9) << 4) + 15] = len;
		var w = Array(80);
		var a = 1732584193;
		var b = -271733879;
		var c = -1732584194;
		var d = 271733878;
		var e = -1009589776;
		for(var i = 0; i < x.length; i += 16) {
			var olda = a;
			var oldb = b;
			var oldc = c;
			var oldd = d;
			var olde = e;
			for(var j = 0; j < 80; j++) {
				if(j < 16) w[j] = x[i + j];
				else w[j] = this.rol(w[j - 3] ^ w[j - 8] ^ w[j - 14] ^ w[j - 16], 1);
				var t = this.safe_add(this.safe_add(this.rol(a, 5), this.sha1_ft(j, b, c,
						d)),
					this.safe_add(this.safe_add(e, w[j]), this.sha1_kt(j)));
				e = d;
				d = c;
				c = this.rol(b, 30);
				b = a;
				a = t;
			}
			a = this.safe_add(a, olda);
			b = this.safe_add(b, oldb);
			c = this.safe_add(c, oldc);
			d = this.safe_add(d, oldd);
			e = this.safe_add(e, olde);
		}
		return Array(a, b, c, d, e);
	}
	/*
	 * Perform the appropriate triplet combination function for the current
	 * iteration
	 */
	this.sha1_ft = function (t, b, c, d) {
		if(t < 20) return(b & c) | ((~b) & d);
		if(t < 40) return b ^ c ^ d;
		if(t < 60) return(b & c) | (b & d) | (c & d);
		return b ^ c ^ d;
	}
	/*
	 * Determine the appropriate additive constant for the current iteration
	 */
	this.sha1_kt = function (t) {
		return(t < 20) ? 1518500249 : (t < 40) ? 1859775393 :
			(t < 60) ? -1894007588 : -899497514;
	}
	/*
	 * Calculate the HMAC-SHA1 of a key and some data
	 */
	this.core_hmac_sha1 = function (key, data) {
		var bkey = this.str2binb(key);
		if(bkey.length > 16) bkey = this.core_sha1(bkey, key.length * this.chrsz);
		var ipad = Array(16),
			opad = Array(16);
		for(var i = 0; i < 16; i++) {
			ipad[i] = bkey[i] ^ 0x36363636;
			opad[i] = bkey[i] ^ 0x5C5C5C5C;
		}
		var hash = this.core_sha1(ipad.concat(this.str2binb(data)), 512 + data.length *
			this.chrsz);
		return this.core_sha1(opad.concat(hash), 512 + 160);
	}
	/*
	 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
	 * to work around bugs in some JS interpreters.
	 */
	this.safe_add = function (x, y) {
		var lsw = (x & 0xFFFF) + (y & 0xFFFF);
		var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
		return(msw << 16) | (lsw & 0xFFFF);
	}
	/*
	 * Bitwise rotate a 32-bit number to the left.
	 */
	this.rol = function (num, cnt) {
		return(num << cnt) | (num >>> (32 - cnt));
	}
	/*
	 * Convert an 8-bit or 16-bit string to an array of big-endian words
	 * In 8-bit function, characters >255 have their hi-byte silently ignored.
	 */
	this.str2binb = function (str) {
		var bin = Array();
		var mask = (1 << this.chrsz) - 1;
		for(var i = 0; i < str.length * this.chrsz; i += this.chrsz)
			bin[i >> 5] |= (str.charCodeAt(i / this.chrsz) & mask) << (24 - i % 32);
		return bin;
	}
	/*
	 * Convert an array of big-endian words to a string
	 */
	this.binb2str = function (bin) {
		var str = "";
		var mask = (1 << this.chrsz) - 1;
		for(var i = 0; i < bin.length * 32; i += this.chrsz)
			str += String.fromCharCode((bin[i >> 5] >>> (24 - i % 32)) & mask);
		return str;
	}
	/*
	 * Convert an array of big-endian words to a hex string.
	 */
	this.binb2hex = function (binarray) {
		var hex_tab = this.hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
		var str = "";
		for(var i = 0; i < binarray.length * 4; i++) {
			str += hex_tab.charAt((binarray[i >> 2] >> ((3 - i % 4) * 8 + 4)) & 0xF) +
				hex_tab.charAt((binarray[i >> 2] >> ((3 - i % 4) * 8)) & 0xF);
		}
		return str;
	}
	/*
	 * Convert an array of big-endian words to a base-64 string
	 */
	this.binb2b64 = function (binarray) {
		var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		var str = "";
		for(var i = 0; i < binarray.length * 4; i += 3) {
			var triplet = (((binarray[i >> 2] >> 8 * (3 - i % 4)) & 0xFF) << 16) | (((
				binarray[i + 1 >> 2] >> 8 * (3 - (i + 1) % 4)) & 0xFF) << 8) | ((binarray[
				i + 2 >> 2] >> 8 * (3 - (i + 2) % 4)) & 0xFF);
			for(var j = 0; j < 4; j++) {
				if(i * 8 + j * 6 > binarray.length * 32) str += this.b64pad;
				else str += tab.charAt((triplet >> 6 * (3 - j)) & 0x3F);
			}
		}
		return str;
	}
});
libroServices.service('xwsse', ['base64', 'sha1',
	function (base64, sha1) {
		this.b64pad = "=";
		this.calc = function (ousername, opassword) {
			var userName = ousername;
			var password = opassword;
			var nonce = this.generateNonce(16);
			var nonce64 = base64.base64encode(nonce);
			var created = this.getW3CDate(new Date());
			var passwordDigest = sha1.b64_sha1(nonce + created + password);
			var digest = "UsernameToken Username=\"" + userName +
				"\", PasswordDigest=\"" + passwordDigest +
				"\", Nonce=\"" + nonce64 + "\", Created=\"" + created + "\"";
			return digest;
		}
		this.generateNonce = function (length) {
			var nonceChars = "0123456789abcdef";
			var result = "";
			for(var i = 0; i < length; i++) {
				result += nonceChars.charAt(Math.floor(Math.random() * nonceChars.length));
			}
			return result;
		}
		this.getW3CDate = function (date) {
			var yyyy = date.getUTCFullYear();
			var mm = (date.getUTCMonth() + 1);
			if(mm < 10) mm = "0" + mm;
			var dd = (date.getUTCDate());
			if(dd < 10) dd = "0" + dd;
			var hh = (date.getUTCHours());
			if(hh < 10) hh = "0" + hh;
			var mn = (date.getUTCMinutes());
			if(mn < 10) mn = "0" + mn;
			var ss = (date.getUTCSeconds());
			if(ss < 10) ss = "0" + ss;
			return yyyy + "-" + mm + "-" + dd + "T" + hh + ":" + mn + ":" + ss + "Z";
		}
	}
]);
libroServices.factory('Socio', ['$resource',
	function ($resource) {
		return $resource('/apisocios', {}, {
			query: {
				method: 'GET',
				interceptor: {
					response: function (data) {
						console.log('response in interceptor', data);
					},
					responseError: function (data) {
						console.log('error in interceptor', data);
					}
				},
				isArray: true
			}
		});
	}
]);