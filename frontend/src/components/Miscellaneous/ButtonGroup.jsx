import React from "react";
import Button from "./Button";
import Container from "./Container";

const ButtonGroup = ({ buttons }) => {
  return (
    <>
      <Container>
        {buttons.map((button) => (
          <Button
            classes={button.classes}
            title={button.title}
            request={button.request}
            link={button.link}
          />
        ))}
      </Container>
    </>
  );
};

export default ButtonGroup;
