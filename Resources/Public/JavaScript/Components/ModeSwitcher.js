import React from 'react';
import { __ } from "@wordpress/i18n";
import styled from "styled-components";

const Switcher = styled.div`
    margin-bottom: 5px;
`;

const Mode = styled.a`
    margin-left: 5px;
`

const SwitcherTitle = styled.p`
  display: inline-block;
  font-size: 90%;
  opacity: 0.7;
`

const ModeText = ({text, active}) => {
    const translatedText = __(text, "yoast-components")
    if (active) {
        return <strong>{translatedText}</strong>
    }
    return translatedText
}

const ModeSwitcher = ({onChange, active}) => {
    return <Switcher>
        <SwitcherTitle>{ __( "Preview as:", "yoast-components" ) }</SwitcherTitle>
        <Mode onClick={() => onChange('mobile')} className={`btn btn-light btn-sm`}><ModeText text={"Mobile result"} active={active === 'mobile'} /></Mode>
        <Mode onClick={() => onChange('desktop')} className={`btn btn-light btn-sm`}><ModeText text={"Desktop result"} active={active === 'desktop'} /></Mode>
    </Switcher>
}

export default ModeSwitcher;
