import React, { useState, useEffect } from "react";
import {
    MDBNavbar, MDBNavbarBrand, MDBNavbarNav, MDBNavItem, MDBCollapse, MDBContainer,
    MDBHamburgerToggler, MDBPopover, MDBPopoverBody, MDBPopoverHeader, MDBIcon
} from 'mdbreact';
import Link from "next/link";
import { useRouter } from "next/router"
import S_STYLES from '../../styles/modules/SubNavbar.module.scss';


export const SubNavbar = ({ header, navBarItems, activePageInd }) => {

    const router = useRouter();
    const path = router.pathname;
    let sortBy = router.query.sortBy;
    let sortDirection = router.query.sortDirection;

    let isActivePageIndEnabled = activePageInd === false ? false : true;

    if (navBarItems && Array.isArray(navBarItems)) {
        navBarItems.forEach((el) => {
            el.isActive = false;
            el.scroll = false;
            if (el.href.indexOf(path) !== -1 && isActivePageIndEnabled === true) {
                el.isActive = true;
            }
            if (el.scroll) {
                el.scroll = true;
            }
        });
    }


    const [url, setUrl] = useState("");
    const [state, setState] = useState({
        isOpen: false
    });

    useEffect(() => {
        setUrl(window.location.href);
    }, [path]);


    const toggleCollapse = () => {
        setState({ isOpen: !state.isOpen });
    }

    return (
        <section className={S_STYLES.subNavbar}>
            <MDBNavbar id="home-plan-sub-navigation" className={`${S_STYLES.navbarCont} p-1`} dark expand="md" scrolling>
                {
                    header && <MDBNavbarBrand className={`${S_STYLES.headerBg}  ml-sm-1 ml-md-3 ml-lg-5`}>
                        <div className={`${S_STYLES.header} ${S_STYLES.borderBtm}`}>{header}</div>
                    </MDBNavbarBrand>
                }
                <MDBHamburgerToggler id="hamburger2" onClick={toggleCollapse} className="d-block d-md-none mx-3" />
                <MDBCollapse isOpen={state.isOpen} navbar style={{ zIndex: "999" }}>
                    <MDBNavbarNav right={header ? true : false} className={`${S_STYLES.menuButton} my-2 mr-md-2 mr-lg-2`}>
                        {navBarItems.map((item, i) => (
                            <MDBNavItem className={item.isActive === true ? S_STYLES.active : S_STYLES.notActive} key={i}>
                                {
                                    sortBy && sortDirection ?
                                        <Link href={ item.href.includes('?') ? item.href + `&sortBy=${sortBy}&sortDirection=${sortDirection}` : item.href + `?sortBy=${sortBy}&sortDirection=${sortDirection}`}>
                                            <div className={`${S_STYLES.title}`}>{item.title}</div>
                                        </Link>
                                        :
                                        <Link href={item.href}>
                                            <div className={`${S_STYLES.title}`}>{item.title}</div>
                                        </Link>
                                }
                            </MDBNavItem>
                        ))}
                        <MDBNavItem className={`${S_STYLES.shareBg} ${S_STYLES.sharePopUp}`}>
                            <MDBPopover placement="top" domElement clickable>
                                <div className={`${S_STYLES.title}`}>Share</div>
                                <MDBPopoverBody>
                                    <div className="my-3">
                                        <a target="_blank" title={encodeURIComponent(`https://www.facebook.com/sharer/sharer.php?u=${url}`)} href={`https://www.facebook.com/sharer/sharer.php?u=${url}`}>
                                            <MDBIcon size="2x" fab icon="facebook-f" />
                                        </a>
                                    </div>
                                    <div className="my-3">
                                        <a target="_blank" title={`https://twitter.com/intent/tweet?text=${url}`} href={`https://twitter.com/intent/tweet?text=${url}`}>
                                            <MDBIcon size="2x" fab icon="twitter" />
                                        </a>
                                    </div>
                                </MDBPopoverBody>
                            </MDBPopover>
                        </MDBNavItem>
                    </MDBNavbarNav>
                </MDBCollapse>
            </MDBNavbar>
        </section>
    )
}


export const SubNavbarHomePlan = ({ filterFn }) => {

    const [homePlansFilter, setHomePlansFilter] = useState([
        { filter: "All", isActive: false, title: "All Home Plans" },
        { filter: "PB", isActive: false, title: "Post And Beam" },
        { filter: "Stacked", isActive: false, title: "Stacked Log Homes" },
        { filter: "Timber", isActive: false, title: "Timber Frame" },
        { filter: "Custom", isActive: false, title: "Custom Design Services" }
    ]);

    const router = useRouter();
    const style = router.query.style;

    const [current, setCurrent] = useState(style ? style : 'All');

    const [state, setState] = useState({
        isOpen: false
    });

    const toggleCollapse = () => {
        setState({ isOpen: !state.isOpen });
    }

    useEffect(() => {
        let newHomePlansFilter = [];

        homePlansFilter.map(homePlanFilter => {
            let isActive = false;
            if (homePlanFilter.filter === current) {
                isActive = true;
            }

            newHomePlansFilter.push({ ...homePlanFilter, isActive: isActive });
        })

        setHomePlansFilter(newHomePlansFilter);
    }, [current]);

    return (
        <section className={S_STYLES.subNavbar}>
            <MDBNavbar className={`${S_STYLES.navbarCont} p-1 mb-3`} dark expand="md" scrolling>
                <MDBNavbarBrand className={`${S_STYLES.headerBg} ml-sm-1 ml-md-3 ml-lg-5`}>
                    <div className={`${S_STYLES.header} ${S_STYLES.borderBtm}`}>Home Plans</div>
                </MDBNavbarBrand>
                <MDBHamburgerToggler id="nav2" onClick={toggleCollapse} className="d-block d-md-none mx-3" />
                <MDBCollapse isOpen={state.isOpen} navbar style={{ zIndex: "999" }}>
                    <MDBNavbarNav right className={`${S_STYLES.menuButton} my-2 mr-md-2 mr-lg-2`}>
                        {homePlansFilter.map((item, i) => (
                            <MDBNavItem className={item.isActive === true ? S_STYLES.active : S_STYLES.notActive} key={i}>
                                <div
                                    onClick={() => {
                                        filterFn(item.filter);
                                        setCurrent(item.filter);
                                    }}
                                    className={`${S_STYLES.title}`}
                                >
                                    {item.title}
                                </div>
                            </MDBNavItem>
                        ))}
                    </MDBNavbarNav>
                </MDBCollapse>
            </MDBNavbar>
        </section>
    )
}
