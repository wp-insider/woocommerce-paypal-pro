/**
 * External dependencies
 */
import { __ } from "@wordpress/i18n";
import { useEffect, useState } from '@wordpress/element';
// const { TextInput } = wc.blocksCheckout;

/**
 * Internal dependencies
 */
import { getSettings } from './Utils';
import styles from './FrontEndContent.module.css';

/**
 * Card type select input component.
 */
function CardTypeSelect({ onBillingCardTypeChange }) {
    const [state, setState] = useState('Visa');
    const handleStateChange = (event) => {
        const inputValue = event.target.value;
        setState(inputValue);
        onBillingCardTypeChange(inputValue)
    };

    useEffect(() => {
        onBillingCardTypeChange(state);
    })

    const options = [
        {
            value: "Visa",
            text: "Visa",
        },
        {
            value: "MasterCard",
            text: "MasterCard",
        },
        {
            value: "Discover",
            text: "Discover",
        },
        {
            value: "Amex",
            text: "American Express",
        },
    ]

    return (
        <select
            id="billing_cardtype"
            name="billing_cardtype"
            title="Billing Card Type"
            onChange={handleStateChange}
        >
            {options.map((item, index) => (
                <option value={item.value} key={index} selected={state == item.value}>{item.text}</option>
            ))}
        </select>
    )
}

/**
 * Card expiry month select input component.
 */
function ExpMonthSelect({ onBillingExpMonthChange }) {
    const months = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];

    const date = new Date();
    const currentMonth = date.getMonth() + 1;

    const [state, setState] = useState(currentMonth);
    const handleStateChange = (event) => {
        const inputValue = event.target.value;
        setState(inputValue);
        onBillingExpMonthChange(inputValue);
    };

    useEffect(() => {
        onBillingExpMonthChange(state);
    })

    return (
        <select
            id="billing_expdatemonth"
            name="billing_expdatemonth"
            title="Billing Card Expiry Month"
            onChange={handleStateChange}
        >
            {months.map((month, index) => (
                <option key={index} value={index + 1} selected={currentMonth === (index+1)}>
                    {month}
                </option>
            ))}
        </select>
    )
}

/**
 * Card expiry year select input component.
 */
function ExpYearSelect({ onBillingExpYearChange }) {
    const date = new Date();
    let year = date.getFullYear();
    let years = [];
    for (let i = 0; i < 12; i++) {
        years.push(year + i);
    }

    const [state, setState] = useState(year);
    const handleStateChange = (event) => {
        const inputValue = event.target.value;
        setState(inputValue);
        onBillingExpYearChange(inputValue);
    };

    useEffect(() => {
        onBillingExpYearChange(state);
    })

    return (
        <select
            id="billing_expdateyear"
            name="billing_expdateyear"
            title="Billing Card Expiry Year"
            onChange={handleStateChange}
        >
            {years.map((year, index) => (
                <option key={index} value={year}>
                    {year}
                </option>
            ))}
        </select>
    );
}

/**
 * Component for site's front-end payment method view. 
 */
const FrontEndContent = (props) => {
    const {
        billing,
        eventRegistration,
        emitResponse,
        components
    } = props;

    const { onPaymentSetup } = eventRegistration;

    // console.log(props);

    const [billingCreditCard, setBillingCreditCard] = useState("");
    const [billingCvvNumber, setBillingCvvNumber] = useState("");
    const [billingCardType, setBillingCardType] = useState("");
    const [billingExpMonth, setBillingExpMonth] = useState("");
    const [billingExpYear, setBillingExpYear] = useState("");

    const card_no_input_placeholder = getSettings('card_number_field_placeholder');
    const cvv_field_placeholder = getSettings('cvv_field_placeholder');
    const securitycodehint = getSettings('securitycodehint');
    const cvv_hint_img = getSettings('cvv_hint_img');
    
    const handleBillingCreditCardChange = (event) => {
        let inputValue = event.target.value 
        setBillingCreditCard(String(inputValue));
    };

    const handleBillingCvvNumberChange = (event) => {
        let inputValue = event.target.value 
        setBillingCvvNumber(String(inputValue));
    };

    const handleBillingCardTypeChange = (cardType) => {
        setBillingCardType(String(cardType));
    };

    const handleBillingExpMonthChange = (expMonth) => {
        setBillingExpMonth(String(expMonth));
    };

    const handleBillingExpYearChange = (expYear) => {
        setBillingExpYear(String(expYear));
    };

    useEffect(() => {
        const unsubscribe = onPaymentSetup(() => {
            // console.log("onPaymentSetup", billingCreditCard, billingCvvNumber, billingCardType, billingExpMonth, billingExpYear);
            return {
                type: emitResponse.responseTypes.SUCCESS,
                meta: {
                    paymentMethodData: {
                        billing_credircard: billingCreditCard,
                        billing_ccvnumber: billingCvvNumber,
                        billing_cardtype: billingCardType,
                        billing_expdatemonth: billingExpMonth,
                        billing_expdateyear: billingExpYear,
                    },
                },
            };
        });

        return () => {
            unsubscribe();
        };
    }, [
        emitResponse.responseTypes.ERROR,
        emitResponse.responseTypes.SUCCESS,
        onPaymentSetup,
        billingCreditCard,
        billingCvvNumber,
        billingCardType,
        billingExpMonth,
        billingExpYear,
    ]);

    return (
        <>
            <fieldset id="wc-paypalpro-cc-form" className="wc-credit-card-form wc-payment-form">
                <div className={styles.formRow + " form-row validate-required"}>
                    <label for="billing_credircard">
                        <span>{__('Card Number', 'woocommerce-paypal-pro-payment-gateway')}</span>
                        <span className={styles.requiredMark}> *</span>
                    </label>
                    <input className="input-text"
                        type="number"
                        min="0"
                        max="9999999999999999"
                        id="billing_credircard"
                        name="billing_credircard"
                        placeholder={card_no_input_placeholder}
                        value={billingCreditCard}
                        onChange={handleBillingCreditCardChange}
                    />
                    {/* <TextInput
                        label={__('Card Number', 'woocommerce-paypal-pro-payment-gateway')}
                        value={billingCreditCard}
                        onChange={handleBillingCreditCardChange}
                    /> */}
                </div>
                <div className={styles.formRow + " form-row"}>
                    <label>
                        <span>{__('Card Type', 'woocommerce-paypal-pro-payment-gateway')}</span>
                        <span className={styles.requiredMark}> *</span>
                    </label>
                    <CardTypeSelect onBillingCardTypeChange={handleBillingCardTypeChange} />
                </div>
                <div className="clear"></div>
                <div className={styles.formRow}>
                    <label>
                        <span>{__('Expiration Date', 'woocommerce-paypal-pro-payment-gateway')}</span>
                        <span className={styles.requiredMark}> *</span>
                    </label>
                    <div style={{ display: "flex" }}>
                        <ExpMonthSelect onBillingExpMonthChange={handleBillingExpMonthChange} />
                        <ExpYearSelect onBillingExpYearChange={handleBillingExpYearChange} />
                    </div>
                </div>
                <div className="clear"></div>
                <div className={styles.formRow + " form-row validate-required"}>
                    <label for="billing_ccvnumber">
                        <span>{__('Card Verification Number (CVV)', 'woocommerce-paypal-pro-payment-gateway')}</span>
                        <span className={styles.requiredMark}> *</span>
                    </label>
                    <input
                        className="input-text"
                        type="number"
                        min="0"
                        max="999"
                        id="billing_ccvnumber"
                        name="billing_ccvnumber"
                        placeholder={cvv_field_placeholder}
                        value={billingCvvNumber}
                        onChange={handleBillingCvvNumberChange}
                    />
                    {/* <TextInput
                        label={__('Card Verification Number (CVV)', 'woocommerce-paypal-pro-payment-gateway')}
                        value={billingCvvNumber}
                        onChange={handleBillingCvvNumberChange}
                    /> */}
                </div>
                <div className="clear"></div>
                {
                    securitycodehint ?
                        <div className={styles.cvvHintWrap}>
                            <img src={cvv_hint_img} alt={__("CVV Code Hint Image", 'woocommerce-paypal-pro-payment-gateway')} />
                        </div>
                        : ""
                }
            </fieldset>
        </>
    )
}

export default FrontEndContent;