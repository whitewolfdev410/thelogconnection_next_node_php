import { MDBContainer, MDBRow, MDBCol } from "mdbreact";
import STYLES from "../../styles/projects/ProjectsDetails.module.scss";
import React, { useState } from "react";

export const PlansDisplaySection = (props) => {

    let plans = [];

    if (props && props.data) {
        plans = props.data;
    }
    else {
        return <h3>Missing Floor Plan</h3>
    }

    return (
        <section className={`${STYLES.plansSection} my-3 my-md-5 mx-2`}>
            <MDBContainer>
                <MDBRow center>
                    {plans.map((img, index) => (
                        <MDBCol md="12" sm="12" key={index} >
                            <img className="disablecopy" src={img} />
                        </MDBCol>
                    ))}
                </MDBRow>
            </MDBContainer>
        </section>
    );
}

