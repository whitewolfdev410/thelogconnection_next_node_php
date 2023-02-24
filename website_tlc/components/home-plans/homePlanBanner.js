import React, { useEffect, useState } from "react";
import { MDBContainer } from "mdbreact";
import STYLES from '../../styles/Common.module.scss';
import { useRouter } from "next/router";
import { homePlanFloorPlanUrl } from "../common/homePlanUrl";

export const HomePlanBanner = ({ img, data }) => {

    const router = useRouter();
    let sortBy = router.query.sortBy;
    let sortDirection = router.query.sortDirection;

    const [prevPlanCode, setPrevPlanCode] = useState(null);
    const [nextPlanCode, setNextPlanCode] = useState(null);
    const [firstPlanCode, setFirstPlanCode] = useState(null);

    useEffect(() => {
        if (data && Object.keys(data).length > 0) {
            if (data.prevHomePlan) {
                setPrevPlanCode(data.prevHomePlan.planCode);
            }

            if (data.nextHomePlan) {
                setNextPlanCode(data.nextHomePlan.planCode);
            }

            if (data.firstHomePlanCode) {
                setFirstPlanCode(data.firstHomePlanCode);
            }
        } else {
            setPrevPlanCode(null);
            setNextPlanCode(null);
            setFirstPlanCode(null);
        }
    }, [data]);

    return (
        <section>
            <MDBContainer fluid className={`${STYLES.bannerCont} ${STYLES.boxShadow1}`}>
                <div className={STYLES.bannerImgCont}>
                    <div style={{ position: 'relative' }}>
                        {
                            prevPlanCode &&
                            <a
                                href={homePlanFloorPlanUrl(prevPlanCode, sortBy, sortDirection)}
                                className={STYLES.homePlanBannerNavigationHandle}
                                style={{ position: 'absolute', top: '50%', left: '50px', fontSize: '2rem', fontWeight: 'Bold' }}
                            >
                                <img className="home-plan-banner-arrow" src='/images/common/prev-arrow-white.png' style={{ boxShadow: 'none' }} />
                            </a>
                        }
                        <img
                            className="d-block w-100 home-plan-banner-image disablecopy"
                            src={img}
                            alt="Home plan image"
                            style={{ pointerEvents: 'none' }}
                        />
                        {
                            (nextPlanCode || firstPlanCode) &&
                            <a
                                href={homePlanFloorPlanUrl(nextPlanCode ? nextPlanCode : firstPlanCode, sortBy, sortDirection)}
                                className={STYLES.homePlanBannerNavigationHandle}
                                style={{ position: 'absolute', top: '50%', right: '50px', fontSize: '2rem', fontWeight: 'Bold' }}
                            >
                                <img className="home-plan-banner-arrow" src='/images/common/next-arrow-white.png' style={{ boxShadow: 'none' }} />
                            </a>
                        }
                    </div>
                </div>
                <div className={STYLES.bannerImgCont}></div>
            </MDBContainer>
        </section>
    )
}