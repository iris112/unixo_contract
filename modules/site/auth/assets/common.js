(function() {
	'use strict';

	// Auto login
	window.autoLogin = function(form) {
		getWeb3(function(web3) {
			form.address.value = web3.eth.coinbase;
			form.submit();
		});

		return false;
	};

	// Registration
	window.registr = function(form) {
		getWeb3(function(web3) {
			web3.eth.getGasPrice(function(err, res) {
				var contract = web3.eth.contract([
						{"inputs":[{"internalType":"uint256","name":"_upline_id","type":"uint256"}],"name":"register","outputs":[],"stateMutability":"payable","type":"function"},
						{"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"users","outputs":[{"internalType":"uint256","name":"id","type":"uint256"},{"internalType":"address","name":"upline","type":"address"},{"internalType":"uint256","name":"balance","type":"uint256"},{"internalType":"uint256","name":"profit","type":"uint256"},{"internalType":"uint8","name":"level","type":"uint8"}],"stateMutability":"view","type":"function"}
					]).at(document.querySelector('[name="eth-contract"]').content),
					reg = function(id) {
						contract.register(id, {
							value: web3.toWei(0.1),
							gasPrice: !err ? parseInt(res.toFixed()) + 7e9 : 15e9,
							gas: 1000000
						}, function(err, tx) {
							if(err) return Notice.error('Error sending transaction');
							Notice.success('Transaction sended. Please wait for the transaction confirmation, after which you can enter the office.');
							document.cookie = 'eth_address=' + web3.eth.coinbase +'; Max-Age=2592000; path=/';
							window.open('https://etherscan.io/tx/' + tx);
							setTimeout(function() { location.href = '/'; }, 5000);
						});
						Notice.warning('Confirm transaction in your Ethereum wallet');
					};

				if(form.upline.value.match(/^0x[a-f0-9]{40}$/i)) {
					contract.users(form.upline.value, {}, function(err, res) {
						if(err) return Notice.error('Error read SmartContract');
						if(res[0].toFixed() > 0) reg(parseInt(res[0].toFixed()));
						else Notice.error('Upline not registered');
					});
				}
				else if(form.upline.value.match(/^[0-9]+$/)) reg(form.upline.value);
				else fetch('/auth/freeReferrer/1/').then(r => r.json()).then(r => reg(r.id));
			});
		});

		return false;
	};
})();