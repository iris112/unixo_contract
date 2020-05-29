(function() {
	'use strict';

	// Buy level
	window.buyLevel = function(level, price) {
		getWeb3(function(web3) {
			web3.eth.getGasPrice(function(err, res) {
				web3.eth.contract([{"inputs":[{"internalType":"uint256","name":"_upline_id","type":"uint256"}],"name":"register","outputs":[],"stateMutability":"payable","type":"function"}]).at(document.querySelector('[name="eth-contract"]').content).register(1, {
					value: web3.toWei(price),
					gasPrice: !err ? parseInt(res.toFixed()) + 7e9 : 15e9
				}, function(err, tx) {
					if(err) return Notice.error('Error sending transaction');

					Notice.success('Transaction sended. Please wait');
				});
				
				Notice.warning('Confirm transaction in your Ethereum wallet');
			});
		});
	};

	document.querySelectorAll('canvas[data-charts]').forEach((v) => {
		new Chart(v.getContext('2d'), JSON.parse(v.getAttribute('data-charts')) || {});
	});
})();