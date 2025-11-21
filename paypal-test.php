<div id="paypal-button-container-P-9JE31567J0740323UNDKSOJY"></div>
<script src="https://www.paypal.com/sdk/js?client-id=AWxtxpBoWiCWmGbD-IC5CO2v9tCHJ5jQzSuf5asOcGbNZ9h1S-5zu0U9ZGtk2kvJWAM57LohBZiz7Tz-&vault=true&intent=subscription" data-sdk-integration-source="button-factory"></script>
<script>
  paypal.Buttons({
      style: {
          shape: 'rect',
          color: 'blue',
          layout: 'vertical',
          label: 'subscribe'
      },
      createSubscription: function(data, actions) {
        return actions.subscription.create({
          /* Creates the subscription */
          plan_id: 'P-9JE31567J0740323UNDKSOJY'
        });
      },
      onApprove: function(data, actions) {
        alert(data.subscriptionID); // You can add optional success message for the subscriber here
      }
  }).render('#paypal-button-container-P-9JE31567J0740323UNDKSOJY'); // Renders the PayPal button
</script>