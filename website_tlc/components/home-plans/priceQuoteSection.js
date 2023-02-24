import React, { useState, useEffect, useRef } from "react";
import { MDBContainer, MDBRow, MDBCol, MDBBtn } from 'mdbreact';
import { US_STATES } from "../../data/states";
import { COUNTRIES } from "../../data/country";
import { CANADA_PROVINCES } from "../../data/canada-provinces";
import { COUNTRY_STATES } from "../../data/country-states";
import { downloadFile } from "../../common/webService";
import { setCookie, getCookie, eraseCookie } from "../../common/cookie";
import 'react-datepicker/dist/react-datepicker.css';
import DatePicker from "react-datepicker";
import STYLES from "../../styles/home-plans/PriceQuote.module.scss";
import { getStockPlanQuantities, getHomePlan, savePriceQuote, calculatePriceQuote, captureUserActivity } from '../../common/services/home-plans';
import { TOAST_NOTIF, ToastNotification } from "../../components/common/toast";
import _ from 'lodash';

export const PriceQuoteSection = (props) => {

    let planCode = props.planCode;

    if (!planCode) {
        return (
            <h2>LOADING...</h2>
        )
    }

    let cookie = {};
    try {
        cookie = JSON.parse(getCookie('PriceQuote_Form'));
        // console.log('cookie', cookie);
    } catch (err) {
        // console.error('cookie', err);
    }

    const [currency, setCurrency] = useState(cookie && cookie.currency ? cookie.currency : "USD"); //CDN
    const [firstName, setFirstName] = useState("");
    const [lastName, setLastName] = useState("");
    const [emailAddress, setEmailAddress] = useState("");
    const [phone, setPhone] = useState("");
    const [buildPlaceOpt, setBuildPlaceOpt] = useState(cookie && cookie.build_place_opt ? cookie.build_place_opt : "USA");
    const [buildPlace, setBuildPlace] = useState(cookie && cookie.build_place ? cookie.build_place : "default");
    const [isSaveCookie, setIsSaveCookie] = useState(true); //todo create a save cookie functionality
    const [planBuildCity, setPlanBuildCity] = useState("");
    const [planBuildCountry, setPlanBuildCountry] = useState("default");
    const [planBuildState, setPlanBuildState] = useState("default");
    const [buildDate, setBuildDate] = useState('');
    const [turnKeyBudget, setTurnKeyBudget] = useState("default");
    const [hasPurchasedLand, setHasPurchasedLand] = useState(false);
    const [hasBlueprint, setHasBlueprint] = useState(false);
    const [addressStreet, setAddressStreet] = useState("");
    const [addressAptNo, setAddressAptNo] = useState("");
    const [addressCity, setAddressCity] = useState("");
    const [addressCountry, setAddressCountry] = useState("default");
    const [addressState, setAddressState] = useState("");
    const [addressPostalCode, setAddressPostalCode] = useState("");
    const [additionalDetails, setAdditionalDetails] = useState('');

    const [planStateArray, setPlanStateArray] = useState([]);
    const [infoStateArray, setInfoStateArray] = useState([]);
    const [activeComponent, setActiveComponent] = useState("PriceQuote");

    const [logStyle, setLogStyle] = useState('Stacked');
    const [logType, setLogType] = useState('Fir');
    const [notch, setNotch] = useState('Saddle');
    const [awbSystem, setAwbSystem] = useState("AWB");
    const [intLogStairs, setIntLogStairs] = useState("CS");
    const [intLogStairRailing, setIntLogStairRailing] = useState("CS");
    const [intLogGuardRailing, setIntLogGuardRailing] = useState("CS");
    const [extDeckRailing, setExtDeckRailing] = useState("CS");

    const [formMatrix, setFormMatrix] = useState({});
    const [breakdown, setBreakdown] = useState({});
    const [totalPriceDisplay, setTotalPriceDisplay] = useState("");
    const [totalPriceRaw, setTotalPriceRaw] = useState(0);
    const [shellPrice, setShellPrice] = useState(0);
    const [materialsPrice, setMaterialsPrice] = useState(0);


    const [quantitiesObj, setQuantitiesObj] = useState({});
    useEffect(() => {
        getStockPlanQuantities(planCode).then((data) => {
            setQuantitiesObj(data?.quantities);
        }).catch((err) => {
            return (
                <h2>ERROR...</h2>
            )
        });
    }, []);

    const [plan, setPlan] = useState({});

    useEffect(() => {
        getHomePlan(planCode).then((data) => {
            if (data && data[0]) {
                setPlan({ ...data[0] });
            } else {
                return (
                    <h2>ERROR...</h2>
                )
            }
        }).catch((err) => {
            return (
                <h2>ERROR...</h2>
            )
        });
    }, [planCode]);


    const handleSubmit = async (event) => {
        let formData = {
            first_name: firstName,
            last_name: lastName,
            email_address: emailAddress,
            phone: phone,
            build_place_opt: buildPlaceOpt,
            build_place: buildPlace === 'default' ? '' : buildPlace,
            is_save_cookie: isSaveCookie,
            currency: currency,
            plan_build_city: planBuildCity,
            plan_build_country: planBuildCountry === 'default' ? '' : planBuildCountry,
            plan_build_state: planBuildState === 'default' ? '' : planBuildState,
            plan_build_date: buildDate,
            turn_key_budget: turnKeyBudget === 'default' ? '' : turnKeyBudget,
            has_purchased_land: hasPurchasedLand,
            has_blueprint: hasBlueprint,
            address_street: addressStreet,
            address_apt_no: addressAptNo,
            address_city: addressCity,
            address_country: addressCountry,
            address_state: addressState,
            address_postal_code: addressPostalCode,
            additional_details: additionalDetails,
            //plan details
            plan_code: planCode,
            plan_name: planCode,
            log_style: logStyle,
            all_weather_barrier: awbSystem,
            log_type: logType,
            notch: notch,
            roofing: '',
            tg_ceiling: '',
            deck: '',
            gables: '',
            floor: '',
            walls: '',
            windows: '',
            windows_extra: '',
            doors: '',
            doors_extra: '',
            log_stair: intLogStairs,
            stair_railing: intLogStairRailing,
            guard_railing: intLogGuardRailing,
            deck_railing: extDeckRailing,
            order_type: 'Price_Quote',
            package: 'Log_Shell',
            shell_price: shellPrice,
            materials_price: materialsPrice,
            total_price: totalPriceRaw
        }
        await savePriceQuote(formData).then(async (res) => {
            ToastNotification(TOAST_NOTIF.PRICE_QUOTE_SUCCESS);
        });
    }

    const saveLocal = async () => {
        event.preventDefault();
        let formData = {
            first_name: firstName,
            last_name: lastName,
            email_address: emailAddress,
            phone: phone,
            build_place_opt: buildPlaceOpt,
            build_place: buildPlace === 'default' ? '' : buildPlace,
            is_save_cookie: isSaveCookie,
            currency: currency,
            plan_build_city: planBuildCity,
            plan_build_country: planBuildCountry === 'default' ? '' : planBuildCountry,
            plan_build_state: planBuildState === 'default' ? '' : planBuildState,
            plan_build_date: buildDate,
            turn_key_budget: turnKeyBudget === 'default' ? '' : turnKeyBudget,
            has_purchased_land: hasPurchasedLand,
            has_blueprint: hasBlueprint,
            address_street: addressStreet,
            address_apt_no: addressAptNo,
            address_city: addressCity,
            address_country: addressCountry,
            address_state: addressState,
            address_postal_code: addressPostalCode,
            additional_details: additionalDetails,
            //plan details
            plan_code: planCode,
            plan_name: planCode,
            log_style: logStyle,
            all_weather_barrier: awbSystem,
            log_type: logType,
            notch: notch,
            roofing: '',
            tg_ceiling: '',
            deck: '',
            gables: '',
            floor: '',
            walls: '',
            windows: '',
            windows_extra: '',
            doors: '',
            doors_extra: '',
            log_stair: intLogStairs,
            stair_railing: intLogStairRailing,
            guard_railing: intLogGuardRailing,
            deck_railing: extDeckRailing,
            order_type: 'Price_Quote',
            package: 'Log_Shell',
            shell_price: shellPrice,
            materials_price: materialsPrice,
            total_price: totalPriceRaw
        }
        if (isSaveCookie) {
            setCookie('PriceQuote_Form', JSON.stringify(formData));
        } else {
            eraseCookie('PriceQuote_Form');
        }

        document.getElementById('home-plan-sub-navigation').scrollIntoView();
        setTimeout(() => {
            setActiveComponent('ViewQuoteSection');
        }, 1000);
        await calculatePrice();
    }

    const downloadPDF = () => {
        let url = `${process.env.PDF_ENDPOINT}`;
        let formData = {
            First_Name: firstName,
            Last_Name: lastName,
            EMail_Address: emailAddress,
            Home_Phone: phone,
            Country_Select: buildPlaceOpt,
            State_Province: buildPlace === 'default' ? '' : buildPlace,
            SaveCookie: isSaveCookie,
            Dollar_Preference: currency,
            Building_City: planBuildCity,
            Building_Country_Select: planBuildCountry === 'default' ? '' : planBuildCountry,
            Building_Location: planBuildState === 'default' ? '' : planBuildState,
            BuildDate: buildDate,
            Budget: turnKeyBudget === 'default' ? '' : turnKeyBudget,
            Do_You_Own_Site: hasPurchasedLand,
            HasBlueprint: hasBlueprint,
            AddressStreet: addressStreet,
            AddressAptNo: addressAptNo,
            City: addressCity,
            AddressCountry: addressCountry,
            AddressState: addressState,
            Zip: addressPostalCode,
            additionalDetails: additionalDetails,
            //plan details
            Plan_Name: planCode,
            Log_Style: logStyle,
            AWB: awbSystem,
            Log_Type: logType,
            Notch: notch,
            Roofing: '',
            TG_Ceiling: '',
            Deck: '',
            Gables: '',
            Floor: '',
            Walls: '',
            Windows: '',
            Windows_Extra: '',
            Doors: '',
            Doors_Extra: '',
            Log_Stair: intLogStairs,
            Stair_Railing: intLogStairRailing,
            Guard_Railing: intLogGuardRailing,
            Deck_Railing: extDeckRailing,
            Order_Type: 'Price_Quote',
            Package: 'Log_Shell',
            shellPrice: shellPrice,
            materialsPrice: materialsPrice,
            totalPrice: totalPriceRaw,
            breakdown: breakdown,
            formMatrix: formMatrix
        }
        let fileName = `${planCode}_Price_Quote.pdf`;
        downloadFile(url, formData, fileName);
    }

    const setPlanCountryStateMapping = (selected) => {
        setPlanBuildCountry(selected);
        let countryObj = COUNTRY_STATES.filter(x => x.country === selected)[0];
        // console.log(countryObj);
        if (countryObj && countryObj.states && countryObj.states.length > 0) {
            // console.log(countryObj.states);
            setPlanStateArray(countryObj.states);
        }
    }

    const setInfoCountryStateMapping = (selected) => {
        setAddressCountry(selected);
        let countryObj = COUNTRY_STATES.filter(x => x.country === selected)[0];
        //console.log(countryObj);
        if (countryObj && countryObj.states && countryObj.states.length > 0) {
            console.log(countryObj.states);
            setInfoStateArray(countryObj.states);
        }
    }

    const getData = () => {

        return {
            first_name: firstName,
            last_name: lastName,
            email_address: emailAddress,
            phone: phone,
            build_place_opt: buildPlaceOpt,
            build_place: buildPlace === 'default' ? '' : buildPlace,
            is_save_cookie: isSaveCookie,
            currency: currency,
            plan_build_city: planBuildCity,
            plan_build_country: planBuildCountry === 'default' ? '' : planBuildCountry,
            plan_build_state: planBuildState === 'default' ? '' : planBuildState,
            plan_build_date: buildDate,
            turn_key_budget: turnKeyBudget === 'default' ? '' : turnKeyBudget,
            has_purchased_land: hasPurchasedLand,
            has_blueprint: hasBlueprint,
            address_street: addressStreet,
            address_apt_no: addressAptNo,
            address_city: addressCity,
            address_country: addressCountry,
            address_state: addressState,
            address_postal_code: addressPostalCode,
            additional_details: additionalDetails,
            //plan details
            plan_code: planCode,
            plan_name: planCode,
            log_style: logStyle,
            all_weather_barrier: awbSystem,
            log_type: logType,
            notch: notch,
            roofing: '',
            tg_ceiling: '',
            deck: '',
            gables: '',
            floor: '',
            walls: '',
            windows: '',
            windows_extra: '',
            doors: '',
            doors_extra: '',
            log_stair: intLogStairs,
            stair_railing: intLogStairRailing,
            guard_railing: intLogGuardRailing,
            deck_railing: extDeckRailing,
            order_type: 'Price_Quote',
            package: 'Log_Shell',
            shell_price: shellPrice,
            materials_price: materialsPrice,
            total_price: totalPriceRaw
        };
    }

    const calculatePrice = async () => {
        let formData = {
            First_Name: firstName,
            Last_Name: lastName,
            EMail_Address: emailAddress,
            Home_Phone: phone,
            Country_Select: buildPlaceOpt,
            State_Province: buildPlace === 'default' ? '' : buildPlace,
            SaveCookie: isSaveCookie,
            Dollar_Preference: currency,
            Building_City: planBuildCity,
            Building_Country_Select: planBuildCountry === 'default' ? '' : planBuildCountry,
            Building_Location: planBuildState === 'default' ? '' : planBuildState,
            BuildDate: buildDate,
            Budget: turnKeyBudget === 'default' ? '' : turnKeyBudget,
            Do_You_Own_Site: hasPurchasedLand,
            HasBlueprint: hasBlueprint,
            AddressStreet: addressStreet,
            AddressAptNo: addressAptNo,
            City: addressCity,
            AddressCountry: addressCountry,
            AddressState: addressState,
            Zip: addressPostalCode,
            additionalDetails: additionalDetails,
            //plan details
            Plan_Name: planCode,
            Log_Style: logStyle,
            AWB: awbSystem,
            Log_Type: logType,
            Notch: notch,
            Roofing: '',
            TG_Ceiling: '',
            Deck: '',
            Gables: '',
            Floor: '',
            Walls: '',
            Windows: '',
            Windows_Extra: '',
            Doors: '',
            Doors_Extra: '',
            Log_Stair: intLogStairs,
            Stair_Railing: intLogStairRailing,
            Guard_Railing: intLogGuardRailing,
            Deck_Railing: extDeckRailing,
            Order_Type: 'Price_Quote',
            Package: 'Log_Shell',
            shellPrice: shellPrice,
            materialsPrice: materialsPrice,
            totalPrice: totalPriceRaw
        };
        const result = await calculatePriceQuote(formData);
        setFormMatrix(result['formMatrix']);
        setBreakdown(result['breakDown']);

        let tempTotal = 0;
        if (result['totalPrice']) {
            tempTotal = Number(result['totalPrice']).toLocaleString('en-US');
            setTotalPriceDisplay(tempTotal);
            setTotalPriceRaw(result['totalPrice']);
        }
        if (result['shellPrice']) {
            setShellPrice(result['shellPrice']);
        }
        if (result['materialsPrice']) {
            setMaterialsPrice(result['materialsPrice'])
        }
        // console.log('calculatePrices', result);
    }

    useEffect(() => {
        calculatePrice(); // This is be executed when `loading` state changes
    }, [logStyle, logType, notch, awbSystem, intLogStairs, intLogStairRailing, intLogGuardRailing, extDeckRailing])

    /*
    |--------------------------------------------------------------------------
    | Get user details
    |--------------------------------------------------------------------------
    |
    */

    const getUserDetails = () => {
        let userDetails = JSON.parse(localStorage.getItem('user'));

        let firstName = userDetails && userDetails.first_name ? userDetails.first_name : '';
        let lastName = userDetails && userDetails.last_name ? userDetails.last_name : '';
        let email = userDetails && userDetails.email ? userDetails.email : '';
        let phone = userDetails && userDetails.phone ? userDetails.phone : '';

        setFirstName(firstName);
        setLastName(lastName);
        setEmailAddress(email);
        setPhone(phone);
    }

    /*
    |--------------------------------------------------------------------------
    | Store user details locally
    |--------------------------------------------------------------------------
    |
    */

    const setUserDetails = () => {
        localStorage.setItem('user', JSON.stringify({
            first_name: firstName,
            last_name: lastName,
            email: emailAddress,
            phone: phone
        }));
    }

    const changeHandler = (value, setStateMehod) => {
        setStateMehod(value);
    };

    const debouncedChangeHandler = _.debounce((value, setStateMethod) => changeHandler(value, setStateMethod), 1000);
    const quickDebouncedChangeHandler = _.debounce((value, setStateMethod) => changeHandler(value, setStateMethod), 1);

    const isFirstRun = useRef(true);

    useEffect(() => {
        getUserDetails();
    }, []);

    useEffect(() => {
        if (isFirstRun.current) {
            isFirstRun.current = false;
            return;
        }

        setUserDetails();
    }, [firstName, lastName, emailAddress, phone]);

    const isLoadingForm = useRef(true);

    useEffect(() => {
        if (isLoadingForm.current) {
            isLoadingForm.current = false;
            return;
        }

        let data = getData();
        captureUserActivity(data)
    }, [
        logStyle,
        awbSystem,
        logType,
        notch,
        intLogStairs,
        intLogStairRailing,
        intLogGuardRailing,
        extDeckRailing
    ]);

    return (
        <>
            <section className={STYLES.priceQuoteSection} style={activeComponent === 'PriceQuote' ? { display: 'block' } : { display: 'none' }}>
                <MDBContainer className={STYLES.priceQuoteCont}>
                    <MDBRow center>
                        <MDBCol md="6" sm="12">
                            <p className={STYLES.formTitle}>INSTANT ON-LINE PRICE QUOTE</p>
                            <p className={STYLES.formSubTitle}>ABOUT YOU <span>(REQUIRED)</span></p>
                            <form onSubmit={saveLocal} autoComplete="off">
                                <MDBRow className={STYLES.formRadio}>
                                    <span>Currency: </span>
                                    <input onClick={() => quickDebouncedChangeHandler('USD', setCurrency, 10)} checked={currency === 'USD'} type="radio" id="radio1" onChange={() => { }} />
                                    <label className="text-left" htmlFor="radio1">US Dollars</label>
                                    <input onClick={() => quickDebouncedChangeHandler('CDN', setCurrency, 10)} checked={currency === 'CDN'} type="radio" id="radio2" onChange={() => { }} />
                                    <label className="text-left" htmlFor="radio2">Canadian Dollars</label>
                                </MDBRow>

                                <MDBRow className={STYLES.formText}>
                                    <MDBCol md="6" sm="12" className="p-0" >
                                        <label className="text-left" htmlFor="firstName">First Name</label>
                                        <input type="text" id="firstName" className={STYLES.inputBox} defaultValue={firstName} onChange={e => debouncedChangeHandler(e.target.value, setFirstName)} required />
                                    </MDBCol>
                                    <MDBCol md="6" sm="12" className="p-0">
                                        <label className="text-left" htmlFor="lastName">Last Name</label>
                                        <input type="text" id="lastName" className={STYLES.inputBox} defaultValue={lastName} onChange={e => debouncedChangeHandler(e.target.value, setLastName)} required />
                                    </MDBCol>

                                </MDBRow>

                                <MDBRow className={STYLES.formText}>
                                    <MDBCol md="6" sm="12" className="p-0">
                                        <label className="text-left" htmlFor="emailAddress">Email Address</label>
                                        <input type="email" id="emailAddress" className={STYLES.inputBox} defaultValue={emailAddress} onChange={e => debouncedChangeHandler(e.target.value, setEmailAddress)} required />

                                    </MDBCol>
                                    <MDBCol md="6" sm="12" className="p-0">
                                        <label className="text-left" htmlFor="phone">Phone</label>
                                        <input type="text" id="phone" className={STYLES.inputBox} defaultValue={phone} onChange={e => debouncedChangeHandler(e.target.value, setPhone)} required />
                                    </MDBCol>
                                </MDBRow>

                                <MDBRow className={STYLES.formRadio}>
                                    <span>Build Country</span>
                                    <input onClick={() => quickDebouncedChangeHandler('USA', setBuildPlaceOpt)} checked={buildPlaceOpt === 'USA'} type="radio" id="radio3" onChange={() => { }} />
                                    <label className="text-left" htmlFor="radio3">USA</label>
                                    <input onClick={() => quickDebouncedChangeHandler('Canada', setBuildPlaceOpt)} checked={buildPlaceOpt === 'Canada'} type="radio" id="radio4" onChange={() => { }} />
                                    <label className="text-left" htmlFor="radio4">Canada</label>
                                    <input onClick={() => quickDebouncedChangeHandler('Other', setBuildPlaceOpt)} checked={buildPlaceOpt === 'Other'} type="radio" id="radio5" onChange={() => { }} />
                                    <label className="text-left" htmlFor="radio5">Other</label>
                                </MDBRow>

                                {buildPlaceOpt === 'USA' ?
                                    <MDBRow className={STYLES.formText}>
                                        <MDBCol md="6" sm="12" className="p-0" >
                                            <label className="text-left">State</label>
                                            <select id="state" name="state" onChange={e => quickDebouncedChangeHandler(e.target.value, setBuildPlace)} className={STYLES.inputBox} required>
                                                <option value={buildPlace} selected={buildPlace === ''}>--Select State--</option>
                                                {US_STATES.map((state, index) => (
                                                    <option value={state.name} key={index}>{state.name}</option>
                                                ))}
                                            </select>
                                        </MDBCol>
                                    </MDBRow> : <></>}

                                {buildPlaceOpt === 'Canada' ?
                                    <MDBRow className={STYLES.formText}>
                                        <MDBCol md="6" sm="12" className="p-0" >
                                            <label className="text-left">Province</label>
                                            <select id="province" name="province" onChange={e => quickDebouncedChangeHandler(e.target.value, setBuildPlace)} className={STYLES.inputBox} required>
                                                <option value={buildPlace} selected={buildPlace === ''}>--Select Province--</option>
                                                {CANADA_PROVINCES.map((province, index) => (
                                                    <option value={province.name} key={index}>{province.name}</option>
                                                ))}
                                            </select>
                                        </MDBCol>
                                    </MDBRow> : <></>}

                                {buildPlaceOpt === 'Other' ?
                                    <MDBRow className={STYLES.formText}>
                                        <MDBCol md="6" sm="12" className="p-0" >
                                            <label className="text-left">Country</label>
                                            <select id="country" name="country" onChange={e => quickDebouncedChangeHandler(e.target.value, setBuildPlace)} className={STYLES.inputBox} required>
                                                <option value={buildPlace} selected={buildPlace === ''}>--Select Country--</option>
                                                {COUNTRIES.map((country, index) => (
                                                    <option value={country.name} key={index}>{country.name}</option>
                                                ))}
                                            </select>
                                        </MDBCol>
                                    </MDBRow> : <></>}

                                <MDBRow className={STYLES.formRadio}>
                                    <input onClick={() => setIsSaveCookie(!isSaveCookie)} checked={isSaveCookie === true} type="radio" id="radio6" onChange={() => { }} />
                                    <label className="text-left" htmlFor="radio6">Remember this information on this computer (future quotes)</label>
                                </MDBRow>

                                <p className={STYLES.formSubTitle}>OPTIONAL INFORMATION</p>
                                <MDBRow className={STYLES.formText}>

                                    <MDBCol md="6" sm="12" className="p-0">
                                        <label className="text-left">Where do you plan to build?</label>
                                    </MDBCol>

                                </MDBRow>

                                <div className="row pb-2">
                                    <div className="col-sm-12 col-md-6 col-lg-6 p-0">
                                        <label className="text-left" style={{ fontSize: '14px', minWidth: '90px', marginRight: '5px' }}>City</label>
                                        <span className={STYLES.formText}>
                                            <input type="text" id="planBuildCity" defaultValue={planBuildCity} onChange={e => debouncedChangeHandler(e.target.value, setPlanBuildCity)} className={STYLES.inputBox} />
                                        </span>
                                    </div>
                                    <div className="col-sm-12 col-md-6 col-lg-6 p-0">
                                        <label className="text-left" style={{ fontSize: '14px', minWidth: '90px', marginRight: '5px' }}>Country</label>
                                        <span className={STYLES.formText}>
                                            <select id="country" name="country" onChange={e => quickDebouncedChangeHandler(e.target.value, setPlanCountryStateMapping)} className={STYLES.inputBox} >
                                                <option value={planBuildCountry} selected={planBuildCountry === 'default'}>--Select Country--</option>
                                                {COUNTRY_STATES.map((cs, index) => (
                                                    <option value={cs.country} key={index}>{cs.country}</option>
                                                ))}
                                            </select>
                                        </span>
                                    </div>
                                </div>

                                <div className="row mb-2">
                                    <div className="col-sm-12 col-md-6 col-lg-6 p-0">
                                        <label className="text-left" style={{ fontSize: '14px', minWidth: '90px', marginRight: '5px' }}>State</label>
                                        <span className={STYLES.formText}>
                                            <select id="planBuildState" name="planBuildState" onChange={e => quickDebouncedChangeHandler(e.target.value, setPlanBuildState)} className={STYLES.inputBox} >
                                                <option value={planBuildState} selected={planBuildState === 'default'}>--Select State--</option>
                                                {planStateArray.map((states, index) => (
                                                    <option value={states} key={index}>{states}</option>
                                                ))}
                                            </select>
                                        </span>
                                    </div>
                                    <div className="col-sm-12 col-md-6 col-lg-6 p-0">
                                        <label className="text-left" style={{ fontSize: '14px', minWidth: '90px', marginRight: '5px' }}>Build Date</label>
                                        <span className="custom-date-picker">
                                            <DatePicker
                                                selected={buildDate}
                                                onChange={(date) => quickDebouncedChangeHandler(date, setBuildDate)}
                                                className={STYLES.datePicker}
                                            />
                                        </span>
                                    </div>
                                </div>

                                <MDBRow className={STYLES.formText}>
                                    <MDBCol md="6" className="p-0">
                                        <label className="text-left align-middle">Turn Key <br /> Budget</label>
                                        <select id="turnKeyBudget" name="turnKeyBudget" onChange={e => quickDebouncedChangeHandler(e.target.value, setTurnKeyBudget)} className={STYLES.inputBox} >
                                            <option value="default">Budget Range</option>
                                            <option value="$ 500,000 to $ 600,000">$ 500,000 to $ 600,000</option>
                                            <option value="$ 600,000 to $ 700,000">$ 600,000 to $ 700,000</option>
                                            <option value="$ 700,000 to $ 800,000">$ 700,000 to $ 800,000</option>
                                            <option value="$ 800,000 to $ 900,000">$ 800,000 to $ 900,000</option>
                                            <option value="Over $ 900,000">Over $ 900,000</option>
                                        </select>
                                    </MDBCol>
                                </MDBRow>

                                <MDBRow className={STYLES.formRadio}>
                                    <MDBCol md="6" className="p-0">
                                        <label className="text-left" htmlFor="">Has purchased land</label>
                                        <input onClick={() => quickDebouncedChangeHandler(true, setHasPurchasedLand)} checked={hasPurchasedLand === true} type="radio" id="hasPurchasedLandYes" onChange={() => { }} />
                                        <label className="text-left" htmlFor="hasPurchasedLandYes">Yes</label>
                                        <input onClick={() => quickDebouncedChangeHandler(false, setHasPurchasedLand)} checked={hasPurchasedLand === false} type="radio" id="hasPurchasedLandNo" onChange={() => { }} />
                                        <label className="text-left" htmlFor="hasPurchasedLandNo">No</label>
                                    </MDBCol>
                                    <MDBCol md="6" className="p-0">
                                        <label className="text-left" htmlFor="">Has Blueprints</label>
                                        <input onClick={() => setHasBlueprint(true)} checked={hasBlueprint === true} type="radio" id="hasBlueprintYes" onChange={() => { }} />
                                        <label className="text-left" htmlFor="hasBlueprintYes">Yes</label>
                                        <input onClick={() => setHasBlueprint(false)} checked={hasBlueprint === false} type="radio" id="hasBlueprintNo" onChange={() => { }} />
                                        <label className="text-left" htmlFor="hasBlueprintNo">No</label>
                                    </MDBCol>
                                    <MDBCol md="12" className="p-0">
                                        <label className="text-left mb-0 mt-3" htmlFor="additionalDetails">Additional details</label>
                                        <textarea type="text" id="additionalDetails" className={'form-control'} defaultValue={additionalDetails} onChange={e => debouncedChangeHandler(e.target.value, setAdditionalDetails)} />
                                    </MDBCol>
                                </MDBRow>

                                <p className={STYLES.formSubTitle + ' mt-3'}>SHIPPING ADDRESS</p>

                                <MDBRow className={STYLES.formText}>
                                    <MDBCol md="6" className="p-0">
                                        <label className="text-left" htmlFor="addressStreet">Street</label>
                                        <input type="text" id="addressStreet" className={STYLES.inputBox} defaultValue={addressStreet} onChange={e => debouncedChangeHandler(e.target.value, setAddressStreet)} />
                                    </MDBCol>
                                    <MDBCol md="6" className="p-0">
                                        <label className="text-left" htmlFor="addressAptNo">APT No.</label>
                                        <input type="text" id="addressAptNo" className={STYLES.inputBox} defaultValue={addressAptNo} onChange={e => debouncedChangeHandler(e.target.value, setAddressAptNo)} />
                                    </MDBCol>
                                </MDBRow>

                                <MDBRow className={STYLES.formText}>
                                    <MDBCol md="6" className="p-0">
                                        <label className="text-left" htmlFor="addressCity">City</label>
                                        <input type="text" id="addressCity" className={STYLES.inputBox} defaultValue={addressCity} onChange={e => debouncedChangeHandler(e.target.value, setAddressCity)} />
                                    </MDBCol>
                                    <MDBCol md="6" className="p-0">
                                        <label className="text-left">Country</label>
                                        <select id="addressCountry" name="addressCountry" onChange={e => setInfoCountryStateMapping(e.target.value)} className={STYLES.inputBox} >
                                            <option value={addressCountry} selected={addressCountry === 'default'}>--Select Country--</option>
                                            {COUNTRY_STATES.map((cs, index) => (
                                                <option value={cs.country} key={index}>{cs.country}</option>
                                            ))}
                                        </select>
                                    </MDBCol>
                                </MDBRow>


                                <MDBRow className={STYLES.formText}>
                                    <MDBCol md="6" className="p-0">
                                        <label className="text-left">State</label>
                                        <select id="addressState" name="addressState" onChange={e => setAddressState(e.target.value)} className={STYLES.inputBox} >
                                            <option value={addressState} selected={addressState === 'default'}>--Select State--</option>
                                            {infoStateArray.map((states, index) => (
                                                <option value={states} key={index}>{states}</option>
                                            ))}
                                        </select>
                                    </MDBCol>
                                    <MDBCol md="6" className="p-0">
                                        <label className="text-left" htmlFor="addressPostalCode">Postal Code</label>
                                        <input type="text" id="addressPostalCode" className={STYLES.inputBox} defaultValue={addressPostalCode} onChange={e => debouncedChangeHandler(e.target.value, setAddressPostalCode)} />
                                    </MDBCol>
                                </MDBRow>

                                <MDBBtn type="submit" size="lg" className={`${STYLES.getQuoteBtn} ${STYLES.getQuoteBtnContainer}`}>
                                    Get Price Quote Now
                                </MDBBtn>
                            </form>
                        </MDBCol>

                        <MDBCol md="6" sm="12">
                            <MDBRow>
                                <MDBCol md="12">
                                    <h2 className={STYLES.planNameCont}>
                                        <sup>THE</sup>{plan?.name} <small>{`${plan?.sf} SQ. FT.`}</small>
                                    </h2>
                                    <img className={`${STYLES.image} disablecopy`} src={plan.imageUrl} />
                                </MDBCol>
                            </MDBRow>
                            <MDBRow>
                                <MDBCol md="3">
                                    <img className={STYLES.stepperImg} src='/images/price-quote-steps/price-quote-step01.png' />
                                </MDBCol>
                                <MDBCol md="3">
                                    <img className={STYLES.stepperImg} src='/images/price-quote-steps/price-quote-step02.png' />
                                </MDBCol>
                                <MDBCol md="3">
                                    <img className={STYLES.stepperImg} src='/images/price-quote-steps/price-quote-step03.png' />
                                </MDBCol>
                                <MDBCol md="3">
                                </MDBCol>
                            </MDBRow>

                        </MDBCol>

                    </MDBRow>
                </MDBContainer>
            </section>
            <section style={activeComponent === 'ViewQuoteSection' ? { display: 'block' } : { display: 'none' }} className={STYLES.priceQuoteSection}>
                <MDBContainer className={STYLES.priceQuoteCont} fluid>
                    <MDBRow>
                        <MDBCol md="6">
                            <p className={STYLES.formTitle}>INSTANT ON-LINE PRICE QUOTE (Step 2 of 3)</p>

                            {/* Building STYLES */}
                            <p className={STYLES.formSubTitle}>Building STYLES</p>
                            <MDBRow className={STYLES.formRadio}>
                                <MDBCol md="12">
                                    <input onClick={() => setLogStyle('Stacked')} checked={logStyle === 'Stacked'} type="radio" id="logStyle1" onChange={() => { }} />
                                    <label className="text-left" htmlFor="logStyle1">Stacked Log Walls <span className={STYLES.priceDiff}>{formMatrix?.Log_Style?.Stacked?.priceDiffText}</span></label>
                                </MDBCol>
                                <MDBCol md="12">
                                    <input onClick={() => setLogStyle('PB')} checked={logStyle === 'PB'} type="radio" id="logStyle2" onChange={() => { }} />
                                    <label className="text-left" htmlFor="logStyle2">Round Log Post and Beam <span className={STYLES.priceDiff}>{formMatrix?.Log_Style?.PB?.priceDiffText}</span></label>
                                </MDBCol>
                                <MDBCol md="12">
                                    <input onClick={() => setLogStyle('Timber')} checked={logStyle === 'Timber'} type="radio" id="logStyle3" onChange={() => { }} />
                                    <label className="text-left" htmlFor="logStyle3">Timber Frame <span className={STYLES.priceDiff}>{formMatrix?.Log_Style?.Timber?.priceDiffText}</span></label>
                                </MDBCol>
                                <MDBCol md="12">
                                    <input onClick={() => setLogStyle('Fusion')} checked={logStyle === 'Fusion'} type="radio" id="logStyle4" onChange={() => { }} />
                                    <label className="text-left" htmlFor="logStyle4">Fusion Style <span className={STYLES.priceDiff}>{formMatrix?.Log_Style?.Fusion?.priceDiffText}</span></label>
                                </MDBCol>
                            </MDBRow>

                            {/* Wood Species */}
                            <p className={STYLES.formSubTitle}>Wood Species</p>
                            <MDBRow className={STYLES.formRadio}>
                                <MDBCol md="12">
                                    <input onClick={() => setLogType('Fir')} checked={logType === 'Fir'} type="radio" id="logType1" onChange={() => { }} />
                                    <label className="text-left" htmlFor="logType1">Douglas Fir <span className={STYLES.priceDiff}>{formMatrix?.Log_Type?.Fir?.priceDiffText}</span></label>
                                </MDBCol>
                                <MDBCol md="12" style={logStyle === 'Timber' ? { display: 'none' } : { display: 'block' }}>
                                    <input onClick={() => setLogType('Cedar')} checked={logType === 'Cedar'} type="radio" id="logType2" onChange={() => { }} />
                                    <label className="text-left" htmlFor="logType2">Western Red Cedar <span className={STYLES.priceDiff}>{formMatrix?.Log_Type?.Cedar?.priceDiffText}</span></label>
                                </MDBCol>
                                <MDBCol md="12" style={(logStyle === 'Timber' || logStyle === 'PB') ? { display: 'none' } : { display: 'block' }}>
                                    <input onClick={() => setLogType('Spruce')} checked={logType === 'Spruce'} type="radio" id="logType3" onChange={() => { }} />
                                    <label className="text-left" htmlFor="logType3">Engelmann Spruce <span className={STYLES.priceDiff}>{formMatrix?.Log_Type?.Spruce?.priceDiffText}</span></label>
                                </MDBCol>
                            </MDBRow>

                            {/* Corner Notching STYLES */}
                            <p className={STYLES.formSubTitle}>Log Wall Corner Notching System</p>
                            {logStyle === 'Timber' ?
                                <MDBRow className={STYLES.formRadio}> <span>-- Corner notches not necessary for Timber Frame style.</span></MDBRow> :
                                <MDBRow className={STYLES.formRadio}>
                                    <MDBCol md="12">
                                        <input onClick={() => setNotch('Saddle')} checked={notch === 'Saddle'} type="radio" id="notch1" onChange={() => { }} />
                                        <label className="text-left" htmlFor="notch1">Scandinavian Saddle Notch <span className={STYLES.priceDiff}>{formMatrix?.Notch?.Saddle?.priceDiffText}</span></label>
                                    </MDBCol>
                                    <MDBCol md="12">
                                        <input onClick={() => setNotch('Chinked')} checked={notch === 'Chinked'} type="radio" id="notch2" onChange={() => { }} />
                                        <label className="text-left" htmlFor="notch2">Chinked Style <span className={STYLES.priceDiff}>{formMatrix?.Notch?.Chinked?.priceDiffText}</span></label>
                                    </MDBCol>
                                    <MDBCol md="12">
                                        <input onClick={() => setNotch('Dovetail_Full')} checked={notch === 'Dovetail_Full'} type="radio" id="notch3" onChange={() => { }} />
                                        <label className="text-left" htmlFor="notch3">Full-Scribe Dovetail <span className={STYLES.priceDiff}>{formMatrix?.Notch?.Dovetail_Full?.priceDiffText}</span></label>
                                    </MDBCol>
                                    <MDBCol md="12" style={(logStyle === 'PB') ? { display: 'none' } : { display: 'block' }}>
                                        <input onClick={() => setNotch('Dovetail_Chinked')} checked={notch === 'Dovetail_Chinked'} type="radio" id="notch4" onChange={() => { }} />
                                        <label className="text-left" htmlFor="notch4">Chinked Dovetail <span className={STYLES.priceDiff}>{formMatrix?.Notch?.Dovetail_Chinked?.priceDiffText}</span></label>
                                    </MDBCol>
                                </MDBRow>}

                            {/* All-Weather Barrier System */}
                            <p className={STYLES.formSubTitle}>All-Weather Barrier System</p>
                            {(logStyle === 'Timber' || logStyle === 'Fusion') || (quantitiesObj && quantitiesObj[planCode] && !quantitiesObj[planCode]['AWB_Price']) ?
                                <MDBRow className={STYLES.formRadio}> <span>-- No all weather barrier required for {logStyle} style.</span></MDBRow> :
                                <MDBRow className={STYLES.formRadio}>
                                    <MDBCol md="12">
                                        <input onClick={() => setAwbSystem('AWB')} checked={awbSystem === 'AWB'} type="radio" id="awbSystem1" onChange={() => { }} />
                                        <label className="text-left" htmlFor="awbSystem1">Yes, include <span className={STYLES.priceDiff}>{formMatrix?.AWB?.AWB?.priceDiffText}</span></label>
                                    </MDBCol>
                                    <MDBCol md="12">
                                        <input onClick={() => setAwbSystem('CS')} checked={awbSystem === 'CS'} type="radio" id="awbSystem2" onChange={() => { }} />
                                        <label className="text-left" htmlFor="awbSystem2">Do not include <span className={STYLES.priceDiff}>{formMatrix?.AWB?.CS?.priceDiffText}</span></label>
                                    </MDBCol>
                                </MDBRow>}

                            {/*Interior Log Staircase */}
                            <p className={STYLES.formSubTitle}>Interior Log Staircase</p>
                            {(quantitiesObj && quantitiesObj[planCode] && quantitiesObj[planCode]['Stair_Treads'] === "0") ?
                                <MDBRow className={STYLES.formRadio}><span>-- No main stairway in this design.</span></MDBRow> :
                                <MDBRow className={STYLES.formRadio}>
                                    <MDBCol md="12">
                                        <input onClick={() => setIntLogStairs('Log_Stair')} checked={intLogStairs === 'Log_Stair'} type="radio" id="intLogStairs1" onChange={() => { }} />
                                        <label className="text-left" htmlFor="intLogStairs1"> Hand crafted log stair <span className={STYLES.priceDiff}>{formMatrix?.Log_Stair?.Log_Stair?.priceDiffText}</span></label>
                                    </MDBCol>
                                    <MDBCol md="12">
                                        <input onClick={() => setIntLogStairs('CS')} checked={intLogStairs === 'CS'} type="radio" id="intLogStairs2" onChange={() => { }} />
                                        <label className="text-left" htmlFor="intLogStairs2"> Do not include <span className={STYLES.priceDiff}>{formMatrix?.Log_Stair?.CS?.priceDiffText}</span></label>
                                    </MDBCol>
                                </MDBRow>}

                            {/* Interior Stair Railing */}
                            <p className={STYLES.formSubTitle}>Interior Stair Railing</p>
                            {(quantitiesObj && quantitiesObj[planCode] && quantitiesObj[planCode]['Stair_Railing'] === "0") ?
                                <MDBRow className={STYLES.formRadio}><span>-- No main stairway in this design.</span></MDBRow> :
                                <MDBRow className={STYLES.formRadio}>
                                    <MDBCol md="12">
                                        <input onClick={() => setIntLogStairRailing('Stair_Railing')} checked={intLogStairRailing === 'Stair_Railing'} type="radio" id="intLogStairRailing1" onChange={() => { }} />
                                        <label className="text-left" htmlFor="intLogStairRailing1">Hand crafted log railing for main stair <span className={STYLES.priceDiff}>{formMatrix?.Stair_Railing?.Stair_Railing?.priceDiffText}</span></label>
                                    </MDBCol>
                                    <MDBCol md="12">
                                        <input onClick={() => setIntLogStairRailing('CS')} checked={intLogStairRailing === 'CS'} type="radio" id="intLogStairRailing2" onChange={() => { }} />
                                        <label className="text-left" htmlFor="intLogStairRailing2"> Do not include <span className={STYLES.priceDiff}>{formMatrix?.Stair_Railing?.CS?.priceDiffText}</span></label>
                                    </MDBCol>
                                </MDBRow>}

                            {/* Interior Guard Railing */}
                            <p className={STYLES.formSubTitle}>Interior Guard Railing</p>
                            {(quantitiesObj && quantitiesObj[planCode] && quantitiesObj[planCode]['Rail_Int_2_LF'] === "0") ?
                                <MDBRow className={STYLES.formRadio}><span>-- No interior guard railing in this design.</span></MDBRow> :
                                <MDBRow className={STYLES.formRadio}>
                                    <MDBCol md="12">
                                        <input onClick={() => setIntLogGuardRailing('All')} checked={intLogGuardRailing === 'All'} type="radio" id="intLogGuardRailing1" onChange={() => { }} />
                                        <label className="text-left" htmlFor="intLogGuardRailing1">Complete, with railing and newel posts <span className={STYLES.priceDiff}>{formMatrix?.Guard_Railing?.All?.priceDiffText}</span></label>
                                    </MDBCol>
                                    <MDBCol md="12">
                                        <input onClick={() => setIntLogGuardRailing('Newels')} checked={intLogGuardRailing === 'Newels'} type="radio" id="intLogGuardRailing2" onChange={() => { }} />
                                        <label className="text-left" htmlFor="intLogGuardRailing2">Newel posts only (no railing) <span className={STYLES.priceDiff}>{formMatrix?.Guard_Railing?.Newels?.priceDiffText}</span></label>
                                    </MDBCol>
                                    <MDBCol md="12">
                                        <input onClick={() => setIntLogGuardRailing('CS')} checked={intLogGuardRailing === 'CS'} type="radio" id="intLogGuardRailing3" onChange={() => { }} />
                                        <label className="text-left" htmlFor="intLogGuardRailing3">Do not include <span className={STYLES.priceDiff}>{formMatrix?.Guard_Railing?.CS?.priceDiffText}</span></label>
                                    </MDBCol>
                                </MDBRow>}

                            {/* Exterior Deck Railing */}
                            <p className={STYLES.formSubTitle}>Exterior Deck Railing </p>
                            {(quantitiesObj && quantitiesObj[planCode] && quantitiesObj[planCode]['Rail_Ext_LF'] === "0") ?
                                <MDBRow className={STYLES.formRadio}><span>-- No exterior deck railing in this design.</span></MDBRow> :
                                <MDBRow className={STYLES.formRadio}>
                                    <MDBCol md="12">
                                        <input onClick={() => setExtDeckRailing('All')} checked={extDeckRailing === 'All'} type="radio" id="extDeckRailing1" onChange={() => { }} />
                                        <label className="text-left" htmlFor="extDeckRailing1">Complete log deck railing <span className={STYLES.priceDiff}>{formMatrix?.Deck_Railing?.All?.priceDiffText}</span></label>
                                    </MDBCol>
                                    <MDBCol md="12">
                                        <input onClick={() => setExtDeckRailing('Newels')} checked={extDeckRailing === 'Newels'} type="radio" id="extDeckRailing2" onChange={() => { }} />
                                        <label className="text-left" htmlFor="extDeckRailing2"> Newel posts only for deck (no railing) <span className={STYLES.priceDiff}>{formMatrix?.Deck_Railing?.Newels?.priceDiffText}</span></label>
                                    </MDBCol>
                                    <MDBCol md="12">
                                        <input onClick={() => setExtDeckRailing('CS')} checked={extDeckRailing === 'CS'} type="radio" id="extDeckRailing3" onChange={() => { }} />
                                        <label className="text-left" htmlFor="extDeckRailing3"> Do not include <span className={STYLES.priceDiff}>{formMatrix?.Deck_Railing?.CS?.priceDiffText}</span></label>
                                    </MDBCol>
                                </MDBRow>}

                            <MDBRow>
                                <MDBCol md="12">
                                    <div className={STYLES.priceQuoteTotal}>
                                        <div className={STYLES.priceQuoteTotalLeft}>
                                            <h5>Log Shell Package Price :</h5>
                                        </div>
                                        <div className={STYLES.priceQuoteTotalRight}>
                                            <h5><span id="display_price">{currency === 'USD' ? 'USD' : 'CDN'} {totalPriceDisplay}</span><small>All amounts in {currency === 'USD' ? 'U. S.' : 'CANADIAN'} dollars.</small></h5>
                                        </div>
                                    </div>
                                </MDBCol>
                            </MDBRow>

                            <MDBRow>
                                <MDBCol md="12">
                                    <MDBBtn size="lg" className={`${STYLES.getQuoteBtn} ${STYLES.getQuoteBtnContainer}`} onClick={() => { downloadPDF(); handleSubmit(); }}> Print and Save Quote</MDBBtn>
                                    <MDBBtn
                                        size="lg"
                                        className={`${STYLES.getQuoteBtn} ${STYLES.getQuoteBtnContainer}`}
                                        onClick={() => {
                                            document.getElementById('home-plan-sub-navigation').scrollIntoView();
                                            setTimeout(() => {
                                                setActiveComponent('PriceQuote');
                                            }, 1000);
                                        }}
                                    >
                                        &#8592; Return back
                                    </MDBBtn>
                                </MDBCol>
                            </MDBRow>

                            <MDBRow>
                                <MDBCol md="3">
                                    <img className={STYLES.stepperImg} src='/images/price-quote-steps/price-quote-step04.png' />
                                </MDBCol>
                                <MDBCol md="3">
                                    <img className={STYLES.stepperImg} src='/images/price-quote-steps/price-quote-step05.png' />
                                </MDBCol>
                                <MDBCol md="3">
                                    <img className={STYLES.stepperImg} src='/images/price-quote-steps/price-quote-step06.png' />
                                </MDBCol>
                                <MDBCol md="3">
                                </MDBCol>
                            </MDBRow>
                        </MDBCol>
                        <MDBCol md="6" sm="12">
                            <MDBRow>
                                <MDBCol md="12">
                                    <h2 className={STYLES.planNameCont}>
                                        <sup>THE</sup>{plan?.name} <small>{`${plan?.sf} SQ. FT.`}</small>
                                    </h2>
                                </MDBCol>
                            </MDBRow>
                            <MDBRow className={STYLES.itemPackageCont}>
                                <MDBCol md="12" sm="6">
                                    <h5>Itemized Log Shell Package Description</h5>
                                    {/* <MDBCol md="12" sm="6" dangerouslySetInnerHTML={{ __html: breakdown?.Log_Type }}></MDBCol> */}
                                    <p style={breakdown && breakdown.Blueprints ? { display: 'block' } : { display: 'none' }}>Blueprints</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Blueprints }}></div>
                                    <p style={breakdown && breakdown.Shell_Wall ? { display: 'block' } : { display: 'none' }}> Shell Wall</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Shell_Wall }}></div>
                                    <p style={breakdown && breakdown.Shell_Roof ? { display: 'block' } : { display: 'none' }}>Shell Roof</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Shell_Roof }}></div>
                                    <p style={breakdown && breakdown.Shell_Floor ? { display: 'block' } : { display: 'none' }}>Shell Floor</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Shell_Floor }}></div>
                                    <p style={breakdown && breakdown.Stair_and_Railings ? { display: 'block' } : { display: 'none' }}>Stair and Railings</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Stair_and_Railings }}></div>
                                    <p style={breakdown && breakdown.Shell_Steel ? { display: 'block' } : { display: 'none' }}>Shell Steel</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Shell_Steel }}></div>
                                    <p style={breakdown && breakdown.Shell_Pre_Delivery ? { display: 'block' } : { display: 'none' }}>Shell Pre-Delivery</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Shell_Pre_Delivery }}></div>
                                    <p style={breakdown && breakdown.Shell_Delivery_Advisor ? { display: 'block' } : { display: 'none' }}>Shell Delivery Advisor</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Shell_Delivery_Advisor }}></div>
                                    <p style={breakdown && breakdown.Roof_Porch ? { display: 'block' } : { display: 'none' }}>Roof Porch</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Roof_Porch }}></div>
                                    <p style={breakdown && breakdown.Gables ? { display: 'block' } : { display: 'none' }}>Gables</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Gables }}></div>
                                    <p style={breakdown && breakdown.Floor ? { display: 'block' } : { display: 'none' }}>Floor</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Floor }}></div>
                                    <p style={breakdown && breakdown.Deck ? { display: 'block' } : { display: 'none' }}>Deck</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Deck }}></div>
                                    <p style={breakdown && breakdown.Walls ? { display: 'block' } : { display: 'none' }}>Walls</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Walls }}></div>
                                    {/* <p style={breakdown && breakdown.Windows ? { display: 'block' } : { display: 'none' }}>Windows</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Windows }}></div>
                                    <p style={breakdown && breakdown.Doors ? { display: 'block' } : { display: 'none' }}>Doors</p>
                                    <div dangerouslySetInnerHTML={{ __html: breakdown?.Doors }}></div> */}
                                </MDBCol>
                            </MDBRow>

                        </MDBCol>
                    </MDBRow>
                </MDBContainer>
            </section>

        </>);
};



