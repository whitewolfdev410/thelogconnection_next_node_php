import React from "react";
import { MDBRow, MDBCol } from "mdbreact";
import Link from "next/link";
import STYLES from "../../styles/projects/HomeOurProjects.module.scss";

export const PlanBookSection = () => {

    return (
        <section className="mt-5">
            <div className="home-page-section">
                <section className={`p-2 p-sm-3 p-md-5 mt-5 ${STYLES.plansSection}`}>
                    <MDBRow>
                        <MDBCol md="6" sm="12">
                            <div className={`p-sm-3 p-md-5 ${STYLES.card}`}>
                                <Link href="/plan-book"><a href="/plan-book">
                                    <div className={STYLES.imgCont}><img className="disablecopy" src='/images/home/book.jpg' /></div>
                                    <div className="m-2"> <div className={`text-center m-3 ${STYLES.title}`}> OUR PLAN BOOK</div></div>
                                    <div className={`mt-4 text-center ${STYLES.subText}`}>This full-color log home plan book features 60 pages of our most requested house plans.</div>
                                </a></Link>
                            </div>
                        </MDBCol>
                        <MDBCol md="6" sm="12">
                            <div className={`p-sm-3 p-md-5  ${STYLES.card}`}>
                                <Link href="/home-plans" replace><a href="/home-plans">
                                    <div className={STYLES.imgCont}><img className="disablecopy" src='/images/home/view_plan.jpg' /></div>
                                    <div className={`text-center ${STYLES.title}`}> VIEW OUR HOUSE PLANS <br/> & <br/> VIRTUAL TOURS ONLINE</div>
                                </a></Link>
                            </div>
                        </MDBCol>
                    </MDBRow>
                </section>
            </div>
        </section>
    );

}
