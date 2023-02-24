import STYLES from '../../styles/modules/HomePlanNav.module.scss';
import React from "react";
import { useRouter } from "next/router"
import { homePlanFloorPlanUrl, homePlanGalleryUrl } from '../common/homePlanUrl';

export const HomePlanNav = (props) => {

    const router = useRouter();

    let prevPlanCode = null;
    let prevPageName = null;
    let nextPlanCode = null;
    let nextPageName = null;

    const nextArrow = {
        backgroundImage: `url('${process.env.DOMAIN}/images/common/next-arrow-bg.png')`, 
        padding: `30px 40px 48px 25px`
    }

    const prevArrow = {
        backgroundImage: `url('${process.env.DOMAIN}/images/common/prev-arrow-bg.png')`,
        padding: `35px 20px 48px 50px`
    }


    if (props.data) {
        if (props.data.prevHomePlan) {
            prevPageName = `${props.data.prevHomePlan.name} ~ ${props.data.prevHomePlan.sf} SQ. FT.`;
            prevPlanCode = props.data.prevHomePlan.planCode;
        }

        if (props.data.nextHomePlan) {
            nextPageName = `${props.data.nextHomePlan.name} ~ ${props.data.nextHomePlan.sf} SQ. FT.`;
            nextPlanCode = props.data.nextHomePlan.planCode;
        }
    }

    return (
        <section className={STYLES.hpNav}>
            {prevPlanCode != null ?
                <a className={STYLES.prevBtnCont} href={props.page === 'IMAGE_GALLERY' ? homePlanGalleryUrl(prevPlanCode) : homePlanFloorPlanUrl(prevPlanCode)} title={prevPlanCode}>
                    {/* <span className={STYLES.label}>Next:</span> */}
                    <div style={prevArrow} className={STYLES.arrow}>{prevPageName}</div>
                </a> : <></>}

            {nextPlanCode != null ?
                <a className={STYLES.nextBtnCont} href={props.page === 'IMAGE_GALLERY' ? homePlanGalleryUrl(nextPlanCode) : homePlanFloorPlanUrl(nextPlanCode)} title={nextPlanCode}>
                    {/* <span className={STYLES.label}>Next:</span> */}
                    <div style={nextArrow} className={STYLES.arrow}>{nextPageName}</div>
                </a> : <></>}
        </section>
    )
}





