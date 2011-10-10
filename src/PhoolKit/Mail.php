<?php
/*
 * PhoolKit - A PHP toolkit.
 * Copyright (C) 2011  Klaus Reimer <k@ailis.de>
 *
 * This library is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at
 * your option) any later version.
 *
 * This library is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser
 * General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace PhoolKit;

use LogicException;

/**
 * A simple mail class which can be used to build and send an email. The
 * character encoding of the email is always UTF-8.
 *
 * @author Klaus Reimer (k@ailis.de)
 */
class Mail
{
    /** The sender contact. */
    private $from;

    /** The recipient contacts. */
    private $to;

    /** The CC recipient contacts. */
    private $cc;

    /** The BCC recipient contacts. */
    private $bcc;

    /** The mail subject. */
    private $subject;

    /** The mail body */
    private $body;

    /**
     * Constructs a new mail.
     */
    public function __construct()
    {
        $this->to = array();
        $this->cc = array();
        $this->bcc = array();
    }
    
    /**
     * Sets the sender.
     * 
     * @param EmailContact $sender
     *            The sender to set. NULL to unset.
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }
    
    /**
     * Returns the sender.
     * 
     * @return EmailContact
     *            The sender or NULL if not set. 
     */
    public function getFrom()
    {
        return $this->from;
    }
    
    /**
     * Sets the mail subject.
     * 
     * @param string $subject
     *            The mail subject to set. NULL to unset.
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
    
    /**
     * Returns the mail subject.
     * 
     * @return string
     *            The mail subject or NULL if not set. 
     */
    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * Sets the mail body.
     * 
     * @param string $body
     *            The mail body to set. NULL to unset.
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Returns the mail body.
     * 
     * @return string
     *             The mail body or NULL if not set.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Adds a recipient.
     * 
     * @param EmailContact $to
     *            The recipient to add.
     */
    public function addTo(EmailContact $to)
    {
        if (!is_array($this->to)) $this->to = array();
        $this->to[] = $to;
    }
    
    /**
     * Sets the recipients. Can be an array or a single EmailContact
     * instance.
     * 
     * @param mixed $to
     *            The recipients to set. An array of EmailContact instances
     *            or a single EmailContact instance.
     */
    public function setTo($to)
    {
        if (is_array($to))
            $this->to = $to;
        else
            $this->to = array($to);
    }
    
    /**
     * Returns the recipients.
     *
     * @return array
     *            The recipients as an array of EmailContact instances.
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Adds a CC recipient address.
     * 
     * @param EmailContact $cc
     *            The CC recipient address to add.
     */
    public function addCc(EmailContact $cc)
    {
        if (!is_array($this->cc)) $this->cc = array();
        $this->cc[] = $cc;
    }

    /**
     * Sets the CC recipients. Can be an array or a single EmailContact
     * instance.
     * 
     * @param mixed $cc
     *            The CC recipients to set. An array of EmailContact instances
     *            or a single EmailContact instance.
     */
    public function setCc($cc)
    {
        if (is_array($cc))
            $this->cc = $cc;
        else
            $this->cc = array($cc);
    }
    
    /**
     * Returns the CC recipients.
     *
     * @return array
     *            The CC recipients as an array of EmailContact instances.
     */
    public function getCc()
    {
        return $this->cc;
    }
    
    /**
     * Adds a BCC recipient.
     * 
     * @param EmailContact $bcc
     *            The BCC recipient to add.
     */
    public function addBcc(EmailContact $bcc)
    {
        if (!is_array($this->bcc)) $this->bcc = array();
        $this->bcc[] = $bcc;
    }
    
    /**
     * Sets the BCC recipients. Can be an array or a single EmailContact
     * instance.
     *
     * @param mixed $bcc
     *            The BCC recipients to set. An array of EmailContact instances
     *            or a single EmailContact instance.
     */
    public function setBcc($bcc)
    {
        if (is_array($bcc))
            $this->bcc = $bcc;
        else
            $this->bcc = array($bcc);
    }
    
    /**
     * Returns the BCC recipients.
     *
     * @return array
     *            The BCC recipients as an array of EmailContact instances.
     */
    public function getBcc()
    {
        return $this->bcc;
    }
    
    /**
     * Returns a string with the named email address of the specified
     * contact.
     *
     * @param EmailContact $contact
     *            The contact.
     * @return string
     *            The string with the named E-Mail address.
     */
    private function toNamedEmailAddress(EmailContact $contact)
    {
        $name = $contact->getName();
        if (!$name) return $contact->getEmail();
        return sprintf("%s <%s>", $this->encodeHeader($name),
            $contact->getEmail());
    }
    
    /**
     * Encodes the specified string so it can be used in the mail header.
     * If text only contains 7 bit characters then it is returned unchanged.
     * Otherwise it is converted to UTF-8 encoded base 64 data.
     * 
     * @param string $text
     *            The text to encode
     * @return string
     *            The encoded text
     */ 
    private function encodeHeader($text)
    {
        if (mb_detect_encoding($text, array("ASCII"), true)) return $text;
        return "=?UTF-8?Q?" . quoted_printable_encode($text) . "?=";
    }
    
    /**
     * Sends the email.
     * 
     * @throws MailException
     *            When mail was rejected by local MTA.
     */
    public function send()
    {
        if (!$this->to && !$this->cc && !$this->bcc)
            throw new LogicException("No mail recipients specified"); 
        
        // Build recipients list
        $recipients = array();
        if ($this->to)
            foreach ($this->to as $recipient)
                $recipients[] = $this->toNamedEmailAddress($recipient);

        // Build headers
        $headers =
            "MIME-Version: 1.0\r\n" .
            "Content-Type: text/plain; charset=UTF-8";
            
        // Add sender
        if ($this->from)
        	"\r\nFrom: " . $this->toNamedEmailAddress($this->from);
        
        // Add CC recipients
        $ccRecipients = array();
        if ($this->cc)
            foreach ($this->cc as $recipient)
                $ccRecipients[] = $this->toNamedEmailAddress($recipient);
        if ($ccRecipients)
            $headers .= "\r\nCC: " . implode(", ", $ccRecipients);
        
        // Add BCC recipients
        $bccRecipients = array();
        if ($this->bcc)
            foreach ($this->bcc as $recipient)
                $bccRecipients[] = $this->toNamedEmailAddress($recipient);
        if ($bccRecipients)
            $headers .= "\r\nBCC: " . implode(", ", $bccRecipients);
        
        // Send the mail
        if (!mail(implode(", ", $recipients), 
            $this->encodeHeader($this->subject), $this->body, $headers,
            $this->from ? ("-f" . $this->from->getEmail()) : ""))
        {
            throw new MailException();
        }
    }    
}
