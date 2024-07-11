/**
 * External dependencies
 */
import {decodeEntities} from '@wordpress/html-entities';
const {registerPaymentMethod} = window.wc.wcBlocksRegistry

/**
 * Internal dependencies
 */
import FrontEndContent from './FrontEndContent';
import {getSettings} from './Utils';
import { PAYMENT_METHOD_NAME } from './Constants.js';

// console.log("WooCommerce PayPal Pro gateway bBlock script loaded!");

const EditPageContent = () => {
    return decodeEntities(getSettings('description', __('Credit / Debit card accept form will render in the front-end.', 'woocommerce-paypal-pro-payment-gateway')));
}

const labelText = decodeEntities(getSettings('title'));
const Label = (props) => {
    const {PaymentMethodLabel, PaymentMethodIcons} = props.components
    const cardIcons = getSettings('cardIcons').map((icon) => {
        return {
            id: icon.id,
            alt: icon.alt,
            src: icon.src
        }
    });

    return (
        <div style={{ width: '100%', display: "flex", justifyContent: 'space-between' }}>
            <PaymentMethodLabel text={labelText} />
            <PaymentMethodIcons icons={cardIcons} align="right"/>
        </div>
    )
}

registerPaymentMethod({
    name: PAYMENT_METHOD_NAME,
    label: <Label/>,
    content: <FrontEndContent/>,
    edit: <EditPageContent/>,
    canMakePayment: () => true,
    ariaLabel: labelText,
    supports: {
        features: getSettings('supports', []),
    }
    // placeOrderButtonLabel: __('Pay With PayPal Pro', 'woocommerce-paypal-pro-payment-gateway'),
})
