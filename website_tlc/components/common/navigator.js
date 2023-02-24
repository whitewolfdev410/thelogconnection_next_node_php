import React from "react";
import STYLES from '../../styles/Common.module.scss';
import Link from "next/link";
import { MDBBtn, MDBContainer, MDBRow, MDBCol } from "mdbreact";
import { useRouter } from "next/router"

export const NavigatorSection = (props) => {

    const router = useRouter();

    const goToLink = (link) => {
        router.push(link, undefined, { scroll: true, shallow: true });
    }

    return (
        <MDBContainer fluid>
            <MDBRow className={STYLES.navigatorBtnCont} style={{ backgroundColor: props?.bgColor }}>
                <MDBCol>
                    <div>
                        <Link href={props.hrefPrev} scroll={true}>
                            <a href={props.hrefPrev}><MDBBtn className={STYLES.prev}>{props.prevLabel}</MDBBtn></a>
                        </Link>
                        {
                            props.hrefNext ?
                                <Link href={props.hrefNext} scroll={true} onClick={() => goToLink(props.hrefPrev)}>
                                    <a href={props.hrefNext}><MDBBtn className={STYLES.next}>{props.nextLabel}</MDBBtn></a>
                                </Link>
                                :
                                <></>
                        }
                    </div>
                </MDBCol>
            </MDBRow>
        </MDBContainer>
    )
}
