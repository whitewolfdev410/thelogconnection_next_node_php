import React from "react";
import Link from "next/link";
import { MDBBtn, MDBIcon } from "mdbreact";
import STYLES from "../../styles/CommonButton.module.scss";

export const ProjectCarouselPrevBtn = () => {
  return (
    <div className={STYLES.projectCarouseLeftBtn}>
      <img src='/images/common/prev.png'/>
    </div>
  )
}

export const ProjectCarouselNextBtn = () => {
  return (
    <div className={STYLES.projectCarouselRightBtn}>
      <img src='/images/common/next.png'/>
    </div>
  )
}

export const CarouselPrevBtn = () => {
  return (
    <div className={STYLES.carouseLeftBtn}>
      <img src='/images/common/media-left-arrow.png' />
    </div>
  )
}

export const CarouselNextBtn = () => {
  return (
    <div className={STYLES.carouselRightBtn}>
      <img src='/images/common/media-right-arrow.png' />
    </div>
  )
}

export const NextPageButton = (props) => {
  const arrow = {
      backgroundImage: `url('${process.env.DOMAIN}/images/common/next-arrow-bg.png')`,
  }
  return (
      <div className={STYLES.nextBtn}>
          <div className={STYLES.nextBtnCont}>
              <span className={STYLES.label}>Next:</span>
              <Link href={props?.pageUrl}>
                  <div style={arrow} className={STYLES.arrow}>{props?.pageName}</div>
              </Link>
          </div>
      </div>
  )
}

export const PrevPageButton = (props) => {
  const arrow = {
      backgroundImage: `url('${process.env.DOMAIN}/images/common/prev-arrow-bg.png')`,
  }
  return (
      <div className={STYLES.nextBtn}>
          <div className={STYLES.nextBtnCont}>
              <span className={STYLES.label}>Next:</span>
              <Link href={props?.pageUrl}>
                  <div style={arrow} className={STYLES.arrow}>{props?.pageName}</div>
              </Link>
          </div>
      </div>
  )
}



export const RedirectButton = (props) => {
    return (
        <span className={STYLES.redirectBtnCont}>
            <Link href={props?.url}>
                <span>
                    <MDBBtn size="lg" className={STYLES.redirectBtn}>
                        <MDBIcon icon="link"></MDBIcon> {props?.label}
                    </MDBBtn>
                </span>
            </Link>
        </span>
    )
}