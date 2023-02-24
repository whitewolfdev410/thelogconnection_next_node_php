import React, { useState } from "react";
import {
    MDBContainer,
    MDBNavbar,
    MDBNavbarBrand,
    MDBNavbarNav,
    MDBCollapse,
    MDBNavItem,
    MDBIcon,
    MDBHamburgerToggler
} from "mdbreact";
import Link from "next/link";
import { useRouter } from "next/router";
import STYLES from "../../styles/Header.module.scss";

const MAIN_PAGES = [
    { href: "/home-plans", match: "/home-plans", title: "HOME PLANS", isActive: false },
    { href: "/building-styles", match: "/building-styles", title: "BUILDING STYLES", isActive: false },
    { href: "/galleries/exterior", match: "/galleries", title: "GALLERIES", isActive: false },
    { href: "/projects/past", match: "/projects", title: "OUR PROJECTS", isActive: false },
    { href: "/tlc/home-of-the-month", match: "/tlc", title: "TLC MONTHLY", isActive: false },
    { href: "/qna", match: "/qna", title: "Q & A", isActive: false }
];

const headerbg = {
    backgroundImage: `url('${process.env.DOMAIN}/images/common/menu-bg.jpg')`,
    backgroundRepeat: "repeat",
    paddingTop: 0,
    paddingBottom: 0,
    maxHeight: "60px",
    zIndex: 2
};


const Header = () => {

    const router = useRouter();
    const path = router.pathname;

    const [state, setState] = useState({
        isOpen: false
    });

    MAIN_PAGES.forEach((el) => {
        el.isActive = false;
        if (path.indexOf(el.match) !== -1) {
            el.isActive = true;
        }
    });


    const handleClickOnLogo = () => {
        setState({ isOpen: false });
    }

    const toggleCollapse = () => {
        setState({ isOpen: !state.isOpen });
    }

    return (
        <div className={STYLES.header}>
            <header>
                <MDBContainer className={STYLES.topHeader} fluid>
                    <div className={STYLES.topHeader}>
                        <a href="mailto:loghomes@thelogconnection.com"><MDBIcon far icon="envelope" className="text-white mr-2" /></a>
                        <a className="d-none d-sm-inline-block mr-5" href="mailto:loghomes@thelogconnection.com">loghomes@thelogconnection.com</a>
                        <a href="tel:+1-888-207-0210"><MDBIcon icon="phone" className="mr-2 text-white" /></a>
                        <span className="d-none d-sm-inline-block text-white">Direct:</span><a className="d-none d-sm-inline-block mr-5" href="tel:+1-250-770-9031">(250) 770-9031</a>
                        <span className="d-none d-sm-inline-block text-white">Toll Free:</span><a className="d-none d-sm-inline-block mr-5" href="tel:+1-888-207-0210">1-888-207-0210</a>
                    </div>
                </MDBContainer>
                <MDBNavbar style={headerbg} expand="md" scrolling className={`${STYLES.navbarCont}`}>
                    <MDBNavbarBrand>
                        <div className="ml-md-3 ml-lg-4">
                            <Link href="/">
                                <img className={`${STYLES.logo} enablecopy`} src='/images/common/logo.png' />
                            </Link>
                        </div>
                    </MDBNavbarBrand>
                    <MDBHamburgerToggler id="hamburger1" onClick={toggleCollapse} className="d-block d-md-none mx-3" />
                    <MDBCollapse isOpen={state.isOpen} navbar style={{ zIndex: "999" }}>
                        <MDBNavbarNav right className={`${STYLES.menuButton} mr-lg-2 text-nowrap`}>
                            {MAIN_PAGES.map((page, i) => (
                                <MDBNavItem key={i} className="my-2 my-md-0">
                                    <span
                                        className={`${page.isActive ? STYLES.selected : STYLES.notSelected} mobile-menu-link`}
                                        onClick={() => document.getElementById('hamburger1').click()}
                                    >
                                        <Link href={page.href}>
                                            {page.title}
                                        </Link>
                                    </span>
                                    <span
                                        className={`${page.isActive ? STYLES.selected : STYLES.notSelected} desktop-menu-link`}
                                    >
                                        <Link href={page.href}>
                                            {page.title}
                                        </Link>
                                    </span>
                                </MDBNavItem>
                            ))}
                        </MDBNavbarNav>
                    </MDBCollapse>
                </MDBNavbar>
            </header>
        </div>
    );
}

export default Header;

