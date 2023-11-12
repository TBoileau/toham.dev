import React from 'react';
import {Client} from 'tmi.js';
import {parse} from "simple-tmi-emotes";

export default function () {
  const [messages, setMessages] = React.useState([]);

  const emoteOptions = {
    format: "default",
    themeMode: "light",
    scale: "2.0",
  };

  const client = new Client({
    channels: ['toham']
  });

  client.on('message', (channel, tags, message) => {
    console.log(tags)
    setMessages((messages) => [...messages, {tags, content: parse(message, tags.emotes, emoteOptions)}]);
  });

  client.on("timeout", (channel, username) => {
    setMessages(messages => [...messages.filter(message => message.tags.username !== username)]);
  });

  client.on("ban", (channel, username) => {
    setMessages(messages => [...messages.filter(message => message.tags.username !== username)]);
  });

  client.on("messagedeleted", (channel, username, deletedMessage, userState) => {
    setMessages(messages => [...messages.filter(message => message.tags.id !== userState["target-msg-id"])]);
  });

  client.on("clearchat", () => setMessages([]));

  React.useEffect(() => {
    client.connect();
  }, []);

  return (
    <>
      {messages.map(message => (
        <article key={message.tags.id} className={'message'}>
          <span className={'message-username'}>{message.tags.username}</span>
          <p className={'message-content'} dangerouslySetInnerHTML={{__html: message.content}}></p>
        </article>
      ))}
    </>
  );
}
