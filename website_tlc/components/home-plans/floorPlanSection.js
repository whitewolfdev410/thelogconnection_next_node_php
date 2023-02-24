import React from "react";
import { MDBContainer, MDBRow, MDBCol } from "mdbreact";
import STYLES from "../../styles/home-plans/FloorPlan.module.scss";

export const FloorPlanSection = (props) => {

    return (
        <div className="floor-plans-container">
            <div className={STYLES.floorPlan}>
                <MDBRow center >
                    {props.floorPlans.map((img, index) => (
                        <MDBCol className="m-0 p-0" md="6" sm="12" key={index}>
                            <div className={STYLES.imgCont}>
                                <img src={img} className="floor-plan-image disablecopy" />
                            </div>
                        </MDBCol>
                    ))}
                </MDBRow>
            </div>
        </div>
    );
}

export default FloorPlanSection;