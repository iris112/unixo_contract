(function() {
    'use strict';

    // Get Web3
    window.getWeb3 = function(cb) {
		if('web3' in window) {
			if(!web3.eth.coinbase) Notice.warning('Please unblock MetaMask or Trust');

	    	let i = 0, id = setInterval(() => {
	    		if(i > 1800 || web3.eth.coinbase) {
	    			clearInterval(id);
					if(web3.eth.coinbase) cb(window.web3);
				}
				else if(++i == 1) {
					if(window.ethereum) {
						window.web3 = new Web3(ethereum);
						ethereum.enable();
					}
					else if(window.web3) window.web3 = new Web3(web3.currentProvider);
				}
	    	}, 100);
		}
		else Notice.error('Please install MetaMask for desktop or Trust for mobile');
    };

	// Copy text
	window.copyText = function(value) {
        var s = document.createElement('input');
        s.value = value;
        document.body.appendChild(s);

        if(navigator.userAgent.match(/ipad|ipod|iphone/i)) {
            s.contentEditable = true;
            s.readOnly = false;
            var range = document.createRange();
            range.selectNodeContents(s);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
            s.setSelectionRange(0, 999999);
        }
        else s.select();

        try { document.execCommand('copy'); Notice.success('Copied'); }
        catch (err) { Notice.error('Copied error'); }

        s.remove();
    };

	document.body.classList.add('web3' in window ? 'web3' : 'noweb3');
})();