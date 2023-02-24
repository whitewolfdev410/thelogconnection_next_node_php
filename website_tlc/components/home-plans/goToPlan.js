import React from "react";
import STYLES from "../../styles/modules/HomePlanNav.module.scss";
import { useRouter } from "next/router";
import Zoom from "react-reveal/Zoom";

export const GoToPlan = ({ homePlans, context }) => {
  const router = useRouter();

  const onSelectPlan = (planCode) => {
    if (planCode) {
      if (context === "price-quote") {
        router.push({
          pathname: "/home-plans/price-quote/" + planCode,
          query: { scroll: false },
        });
      } else {
        router.push({
          pathname: "/home-plans/details/floor-plans/" + planCode,
          query: { scroll: false },
        });
      }
    }
  };

  return (
    <>
      {homePlans && homePlans.length > 0 ? (
        <Zoom>
          <div className={`${STYLES.goToPlan}`}>
            <select
              id="state"
              name="state"
              onChange={(e) => onSelectPlan(e.target.value)}
              className="browser-default custom-select"
            >
              <option value="">Go To Plan</option>
              {homePlans.map((hp, i) => (
                <option value={hp.planCode} key={i}>
                  {hp.name}
                </option>
              ))}
            </select>
          </div>
        </Zoom>
      ) : (
        <></>
      )}
    </>
  );
};
